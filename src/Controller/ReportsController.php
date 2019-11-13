<?php

namespace App\Controller;

use Cake\Cache\Cache;
use Cake\I18n\Time;
use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use App\Utility\Location;
use Cake\ORM\Table;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
/**
 * Dashboard Controller
 *
 * @property \App\Model\Table\ClientsTable $Clients
 */
class ReportsController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Tools.AuthUser');
		
		$this->loadModel('Users');
		$this->loadModel('Attendances'); 
		$this->loadModel('Organizations');
		$this->loadModel('User_designations');
		$this->loadModel('Designations');
		$this->loadComponent('RequestHandler');
    }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
		$this->loadModel('Users');
        $this->loadModel('UserOrganizations');
       
		$Users = TableRegistry::get('Users');
		$Users = TableRegistry::getTableLocator()->get('Users');
		$departments = TableRegistry::get('Organizations');
		$departments = TableRegistry::getTableLocator()->get('Organizations');
		$dateselected = $this->request->query['date_attendance'];
		$departmentSelected = $this->request->query('department');
        $userSelected = $this->request->query('user');
        $filterSelected = $this->request->query('filterby');
		$userId = $this->AuthUser->id();
        $user = $this->Users->find()->contain(['Roles'])->Where(['id' => "$userId"])->limit(1)->first();
        $userRoles = $this->Users->Roles->initRolesChecker($user->roles);
		$users = $this->Users->find('list');
		
        $departments = $this->Organizations->find('list');
		
		//department session
		$sqldepartment = "SELECT * FROM user_organizations WHERE user_id='".$userId."'";
		
		$connection = ConnectionManager::get('default');
		$resultsDepartment = $connection->execute($sqldepartment)->fetchAll('assoc');
		$deptId = $resultsDepartment[0]['organization_id'];
		
		if ($userRoles->hasRole(['Admin','Staff'])) {
			$userSelected 		= $userId;
			$departmentSelected = $deptId;
        }
		if ($userRoles->hasRole(['Supervisor'])) {
			$departmentSelected = $deptId;
        }
		
		if (($userRoles->hasRole(['Master Admin']) || $userRoles->hasRole(['Supervisor'])) && $departmentSelected) {
		   $users = $this->Users->find('list')->order(['Users.name' => 'ASC'])->innerJoinWith('UserOrganizations.Organizations' , function($q) use($departmentSelected){
            return $q->where(['UserOrganizations.organization_id'=>$departmentSelected])->where(['Users.status'=>1]);});
	    }
		
		
		$tempYear	= date("Y");
		$tempMonth	= date("m");
		$tempDay 	= date("d");
		
		if ($dateselected){
			$tempYear	=date("Y",strtotime($dateselected));
			$tempMonth	=date("m",strtotime($dateselected));
			$tempDay	=date("d",strtotime($dateselected));
		} else {
			$dateselected = date( 'Y-m-d');
		}
		
		
		/* $sql = "SELECT Users.*, Attendances.cdate as att_date,Attendances.attn_time, Attendances.attn_remarks,UserCards.card_colour,Organization.name as organization_name
			FROM users Users
			LEFT JOIN (
				SELECT Attendances.cdate,Attendances.status,Attendances.user_id, 
				GROUP_CONCAT(DISTINCT Attendances.cdate SEPARATOR '||') AS attn_time,
				GROUP_CONCAT(DISTINCT Attendances.remarks SEPARATOR ',') AS attn_remarks
				FROM attendances Attendances 
				WHERE date(Attendances.cdate) = '".$dateselected."'
				GROUP BY Attendances.user_id
			)Attendances ON Users.id = Attendances.user_id
			LEFT JOIN (
				SELECT ucards.user_id,c.name as card_colour FROM user_cards ucards
				LEFT JOIN cards c ON ucards.card_id=c.id 
				WHERE date(ucards.cdate) = '".$dateselected."'
				GROUP BY ucards.user_id
			) UserCards ON UserCards.user_id = Users.id
			LEFT JOIN user_organizations Uorganization ON Uorganization.user_id = Users.id			
			LEFT JOIN organizations Organization ON Uorganization.organization_id = Organization.id
			WHERE Users.status = 1"; */
			
		$sql = "SELECT Users.*, Attendances.cdate as attn_date,Attendances.in_time,Attendances2.out_time, Attendances.remarks,UserCards.card_colour,Organization.name as organization_name,Attendances.late_status,Attendances.late_remark,uleave.reason
			FROM users Users
			LEFT JOIN (
				SELECT Attendances.cdate,Attendances.cdate as in_time,Attendances.status,Attendances.user_id,Attendances.remarks,attn_late.status as late_status, attn_late.late_remark
				FROM attendances Attendances 
				LEFT JOIN attendance_lates attn_late ON attn_late.attendance_id=Attendances.id
				WHERE date(Attendances.cdate) = '".$dateselected."' and Attendances.status=1
				GROUP BY Attendances.user_id
			)Attendances ON Users.id = Attendances.user_id
			LEFT JOIN (
				SELECT Attendances2.cdate as out_time,Attendances2.user_id
				FROM attendances Attendances2 
				WHERE date(Attendances2.cdate) = '".$dateselected."' and Attendances2.status=2
				GROUP BY Attendances2.user_id
			)Attendances2 ON Users.id = Attendances2.user_id
			LEFT JOIN (
				SELECT ucards.user_id,c.name as card_colour FROM user_cards ucards
				LEFT JOIN cards c ON ucards.card_id=c.id 
				WHERE date(ucards.cdate) = '".$dateselected."'
				GROUP BY ucards.user_id
			) UserCards ON UserCards.user_id = Users.id
			LEFT JOIN (
				SELECT uleave.reason, uleave.user_id
				FROM user_leaves uleave
				WHERE date(uleave.date_start) <= '".$dateselected."' 
				AND date(uleave.date_end) >= '".$dateselected."'
				AND uleave.status = 1
				GROUP BY uleave.user_id
			) uleave ON uleave.user_id = Users.id
			LEFT JOIN user_organizations Uorganization ON Uorganization.user_id = Users.id			
			LEFT JOIN organizations Organization ON Uorganization.organization_id = Organization.id
			WHERE Users.status = 1";
		
		
		
		if ($departmentSelected){
			$sql .= " AND Uorganization.organization_id = '".$departmentSelected."'";
		}
		if ($userSelected){
			$sql .= " AND Users.id = '".$userSelected."'";
		}
		$sql .= " ORDER BY Users.card_no";
		
		$results = $connection->execute($sql)->fetchAll('assoc');
		
		$this->viewBuilder()->options([
            'pdfConfig' => [
                'orientation' => 'landscape',
                'filename' => 'DailyReport.pdf'
            ]
        ]);

		$this->set('result',$results);
        $this->set(compact('result','deptId','sql','dateselected', 'userRoles','departments','users','filterSelected','departmentSelected','userSelected'));
		$this->set('_serialize', ['report']);
    }
	
	public function weekly()
    {
       
		$Users = TableRegistry::get('Users');
		$Users = TableRegistry::getTableLocator()->get('Users');
		$departments = TableRegistry::get('Organizations');
		$departments = TableRegistry::getTableLocator()->get('Organizations');
		
		$dateselected = $this->request->query['date_attendance'];
		$departmentSelected = $this->request->query('department');
        $userSelected = $this->request->query('user');
		$userId = $this->AuthUser->id();
        $user = $this->Users->find()->contain(['Roles'])->Where(['id' => "$userId"])->limit(1)->first();
        $userRoles = $this->Users->Roles->initRolesChecker($user->roles);
		$users = $this->Users->find('list');
        $departments = $this->Organizations->find('list');
		$thisweekStart	= date('Y-m-d', strtotime( 'sunday last week' ) );
		$thisweekEnd	= date( 'Y-m-d', strtotime( 'saturday this week' ) );
		
		//department session
		//department session
		$sqldepartment = "SELECT * FROM user_organizations WHERE user_id='".$userId."'";
		
		$connection = ConnectionManager::get('default');
		$resultsDepartment = $connection->execute($sqldepartment)->fetchAll('assoc');
		$deptId = $resultsDepartment[0]['organization_id'];
		
		if ($userRoles->hasRole(['Admin','Staff'])) {
			$userSelected 		= $userId;
			$departmentSelected = $deptId;
        }
		
		if ($userRoles->hasRole(['Supervisor'])) {
			$departmentSelected = $deptId;
        }		
		if (($userRoles->hasRole(['Master Admin']) || $userRoles->hasRole(['Supervisor'])) && $departmentSelected) {
		   $users = $this->Users->find('list')->order(['Users.name' => 'ASC'])->innerJoinWith('UserOrganizations.Organizations' , function($q) use($departmentSelected){
            return $q->where(['UserOrganizations.organization_id'=>$departmentSelected])->where(['Users.status'=>1]);});
	    }
		
		if ($dateselected){
			$thisweekStart	= date('Y-m-d', strtotime( 'sunday last week',strtotime($dateselected)));
			$thisweekEnd	= date('Y-m-d', strtotime( 'saturday this week',strtotime($dateselected)));
		}else {
			$dateselected = date( 'Y-m-d');
		}
		
		$weeklysql = "SELECT Users.*,Attendances.total_late,cardinfo.card_colour as card_colour, 
				Organization.name as 	organization_name ,Attendances.approved_late
				FROM users Users
				LEFT JOIN (
					SELECT count(Attendances.user_id) as total_late,Attendances.cdate,Attendances.user_id,
					count(attn_late.status=1) as approved_late
					FROM attendances Attendances 
					LEFT JOIN attendance_lates attn_late ON attn_late.attendance_id=Attendances.id
					WHERE  DATE_FORMAT(Attendances.cdate, '%Y-%m-%d')>='".$thisweekStart."'
					AND DATE_FORMAT(Attendances.cdate, '%Y-%m-%d')<='".$thisweekEnd."'
					AND DATE_FORMAT(Attendances.cdate, '%H:%i:%s')>='09:00:00'
					AND Attendances.status = 1 
					GROUP BY Attendances.user_id
				)Attendances ON Users.id = Attendances.user_id
				LEFT JOIN (
					SELECT Ucards.cdate,Ucards.card_id,Ucards.user_id,Ucards.remarks,Cards.name as card_colour
					from user_cards Ucards
					LEFT JOIN cards Cards ON Cards.id=Ucards.card_id 
					WHERE  DATE_FORMAT(Ucards.cdate, '%Y-%m-%d')>='".$thisweekStart."'
					AND DATE_FORMAT(Ucards.cdate, '%Y-%m-%d')<='".$thisweekEnd."'
					ORDER by cdate Desc
					LIMIT 18446744073709551615
				) cardinfo ON Users.id =cardinfo.user_id
				LEFT JOIN user_organizations Uorganization ON Uorganization.user_id = Users.id
				LEFT JOIN organizations Organization ON Uorganization.organization_id = Organization.id
				WHERE Users.status = 1";
		
		
		
		if ($departmentSelected){
			$weeklysql .= " AND Uorganization.organization_id = '".$departmentSelected."'";
		}
		if ($userSelected){
			$weeklysql .= " AND Users.id = '".$userSelected."'";			
		}
		
		$weeklysql .= " GROUP BY  Users.id
				ORDER BY Users.card_no";
		
		$weeklyresults = $connection->execute($weeklysql)->fetchAll('assoc');
		
		$this->viewBuilder()->options([
            'pdfConfig' => [
                'orientation' => 'landscape',
                'filename' => 'WeeklyReport.pdf'
            ]
        ]);
		
		$this->set('weeklyresult',$weeklyresults);
		$this->set(compact('weeklyresult', 'weeklysql','dateselected','thisweekStart','thisweekEnd','userRoles','departments','users','departmentSelected','userSelected'));
		$this->set('_serialize', ['report']);
    }
	public function monthly()
    {
       
		$Users = TableRegistry::get('Users');
		$Users = TableRegistry::getTableLocator()->get('Users');
		$departments = TableRegistry::get('Organizations');
		$departments = TableRegistry::getTableLocator()->get('Organizations');
			
		$departmentSelected = $this->request->query('department');
        $userSelected = $this->request->query('user');
        $monthselected = $this->request->query('att_month');
        $yearselected = $this->request->query('att_year');
		
		$userId = $this->AuthUser->id();
        $user = $this->Users->find()->contain(['Roles'])->Where(['id' => "$userId"])->limit(1)->first();
        $userRoles = $this->Users->Roles->initRolesChecker($user->roles);
		$users = $this->Users->find('list');
        $departments = $this->Organizations->find('list');
		$thismonthStart	= date('Y-m-1');
		$thismonthEnd	= date( 'Y-m-t');
		
		//department session
		$sqldepartment = "SELECT * FROM user_organizations WHERE user_id='".$userId."'";
		
		$connection = ConnectionManager::get('default');
		$resultsDepartment = $connection->execute($sqldepartment)->fetchAll('assoc');
		$deptId = $resultsDepartment[0]['organization_id'];
		
		if ($userRoles->hasRole(['Admin','Staff'])) {
			$userSelected 		= $userId;
			$departmentSelected = $deptId;
        }
		if ($userRoles->hasRole(['Supervisor'])) {
			$departmentSelected = $deptId;
        }
		
		if (($userRoles->hasRole(['Master Admin']) || $userRoles->hasRole(['Supervisor'])) && $departmentSelected) {
		   $users = $this->Users->find('list')->order(['Users.name' => 'ASC'])->innerJoinWith('UserOrganizations.Organizations' , function($q) use($departmentSelected){
            return $q->where(['UserOrganizations.organization_id'=>$departmentSelected])->where(['Users.status'=>1]);});
	    }
		
		if ($monthselected){
			$thismonthStart1	=date("Y-".$monthselected."-01");
			$thismonthEnd		=date("Y-".$monthselected."-t");
		} else {
			$monthselected	= date('m');
			$thismonthStart	= date("Y-".$monthselected."-01");
			$thismonthEnd	= date("Y-".$monthselected."-t");
		}
		if ($yearselected){
			$thismonthStart	=date($yearselected."-".$monthselected."-01");
			$thismonthEnd	=date($yearselected."-".$monthselected."-t");
		} else {
			$yearselected	= date('Y');
			$thismonthStart	= date("Y-".$monthselected."-01");
			$thismonthEnd	= date("Y-".$monthselected."-t");
		}
		
		$monthlysql = "SELECT Users.*,Attendances.total_late,cardinfo.card_colour as card_colour,
				cardinfo.remarks as card_remarks,cardinfo.cdate,Organization.name as organization_name,g.name as grade, Attendances.approved_late
				FROM users Users
				LEFT JOIN grades g ON g.id = Users.grade_id				
				LEFT JOIN user_organizations UserOrganization ON UserOrganization.user_id = Users.id				
				LEFT JOIN organizations Organization ON UserOrganization.organization_id = Organization.id
				LEFT JOIN (
					SELECT count(DISTINCT(DAY(Attendances.cdate))) as total_late,Attendances.cdate,Attendances.user_id,
					count(attn_late.status=1) as approved_late
					FROM attendances Attendances 
					LEFT JOIN attendance_lates attn_late ON attn_late.attendance_id=Attendances.id
					WHERE  DATE_FORMAT(Attendances.cdate, '%Y-%m-%d')>='".$thismonthStart."'
					AND DATE_FORMAT(Attendances.cdate, '%Y-%m-%d')<='".$thismonthEnd."'
					AND DATE_FORMAT(Attendances.cdate, '%H:%i:%s')>='09:00:00'
					AND Attendances.status = 1 
					GROUP BY Attendances.user_id
				)Attendances ON Users.id = Attendances.user_id
				LEFT JOIN ( 
					SELECT Ucards.cdate,Ucards.card_id,Ucards.user_id,Ucards.remarks,Cards.name as card_colour
					FROM user_cards Ucards
					LEFT JOIN cards Cards ON Cards.id=Ucards.card_id 
					WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."'
					AND DATE_FORMAT(Ucards.cdate, '%Y')='".$yearselected."'
					ORDER by cdate ASC
					LIMIT 18446744073709551615 
				)cardinfo ON cardinfo.user_id=Users.id 
				WHERE Users.status = 1";
		
		if ($departmentSelected){
			$monthlysql .= " AND UserOrganization.organization_id = '".$departmentSelected."'";
		}
		if ($userSelected){
			$monthlysql .= " AND Users.id = '".$userSelected."'";
		}
		$monthlysql .= " GROUP BY Users.id ORDER BY Users.card_no";
				
		$this->viewBuilder()->options([
            'pdfConfig' => [
                'orientation' => 'landscape',
                'filename' => 'MonthlyReport.pdf'
            ]
        ]);
		$connection = ConnectionManager::get('default');
		$monthlyresults = $connection->execute($monthlysql)->fetchAll('assoc');
		$this->set('monthlyresult',$monthlyresults);
        $this->set(compact('monthlyresult', 'monthlysql','monthselected','yearselected','userRoles','departments','users','departmentSelected','userSelected'));
        $this->set('_serialize', ['report']);
    }
	
	public function summary()
    {
		$connection = ConnectionManager::get('default');
		
		$Users = TableRegistry::get('Users');
		$Users = TableRegistry::getTableLocator()->get('Users');
		$departments = TableRegistry::get('Organizations');
		$departments = TableRegistry::getTableLocator()->get('Organizations');
			
		$departmentSelected = $this->request->query('department');
        $userSelected = $this->request->query('user');
        $monthselected = $this->request->query('att_month');
        $yearselected = $this->request->query('att_year');
		
		$userId = $this->AuthUser->id();
        $user = $this->Users->find()->contain(['Roles'])->Where(['id' => "$userId"])->limit(1)->first();
        $userRoles = $this->Users->Roles->initRolesChecker($user->roles);
		$users = $this->Users->find('list');
        $departments = $this->Organizations->find('list');
		$thismonthStart	= date('Y-m-01');
		$thismonthEnd	= date( 'Y-m-t');
		
		if ($monthselected){
			$thismonthStart	=date("Y-".$monthselected."-01");
			$thismonthEnd	=date("Y-".$monthselected."-t");
		} else {
			$monthselected	= date('m');
		}
		
		if ($yearselected){
			$thismonthStart	=date($yearselected."-".$monthselected."-1");
			$thismonthEnd	=date($yearselected."-".$monthselected."-t");
		} else {
			$yearselected	= date('Y');
		}
		
		//result for grade 55 and above
		$grade55sql = "SELECT count(u.id) as total_officer,count(Ucards.green) as green,count(Ucards2.yellow) as yellow,
						count(Ucards3.red) as red
						FROM users u
						LEFT JOIN (
							SELECT card_id AS green,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND 
							DATE_FORMAT(Ucards.cdate, '%Y')='".$yearselected."'
							AND card_id =1
							AND Ucards.cdate IN (SELECT MAX(Ucards.cdate) from user_cards Ucards group by Ucards.user_id)
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards ON u.id = Ucards.user_id
						LEFT JOIN (
							SELECT card_id as yellow,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND
							DATE_FORMAT(Ucards.cdate, '%Y')='".$yearselected."' AND card_id =2
							AND Ucards.cdate IN (SELECT MAX(Ucards.cdate) from user_cards Ucards group by Ucards.user_id)
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC	
						)Ucards2 ON u.id = Ucards2.user_id
						LEFT JOIN (
							SELECT card_id as red,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND
							DATE_FORMAT(Ucards.cdate, '%Y')='".$yearselected."' AND card_id =3
							AND Ucards.cdate IN (SELECT MAX(Ucards.cdate) from user_cards Ucards group by Ucards.user_id)
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards3 ON u.id = Ucards3.user_id
						WHERE u.skim >= 55
						ORDER BY u.id";
		$grade55results = $connection->execute($grade55sql)->fetchAll('assoc');
		$this->set('grade55result',$grade55results);
		
		$totallate55sql = "SELECT count(DISTINCT(DAY(Attendances.cdate))) as total_late,Attendances.cdate,Attendances.user_id,
							Attendances.remarks
							FROM attendances Attendances 
							LEFT JOIN users u ON Attendances.user_id= u.id
							WHERE MONTH(Attendances.cdate)= '".$monthselected."'
							AND YEAR(Attendances.cdate)= '".$yearselected."'
							AND DATE_FORMAT(Attendances.cdate, '%H:%i:%s')>='09:00:00'
							AND Attendances.status = 1 
							AND u.skim >= 55 
							GROUP BY Attendances.user_id";
		$totallate55results = $connection->execute($totallate55sql)->fetchAll('assoc');
		$this->set('totallate55result',$totallate55results);
		
		//result for grade 48 to 54
		$grade4854sql = "SELECT count(u.id) as total_officer,count(Ucards.green) as green,count(Ucards2.yellow) as yellow,
						count(Ucards3.red) as red
						FROM users u
						LEFT JOIN (
							SELECT card_id AS green,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND
							DATE_FORMAT(Ucards.cdate, '%Y')='".$yearselected."' AND card_id =1
							AND Ucards.cdate IN (SELECT MAX(Ucards.cdate) from user_cards Ucards group by Ucards.user_id)
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards ON u.id = Ucards.user_id
						LEFT JOIN (
							SELECT card_id as yellow,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND
							DATE_FORMAT(Ucards.cdate, '%Y')='".$yearselected."' AND card_id =2
							AND Ucards.cdate IN (SELECT MAX(Ucards.cdate) from user_cards Ucards group by Ucards.user_id)
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC	
						)Ucards2 ON u.id = Ucards2.user_id
						LEFT JOIN (
							SELECT card_id as red,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND
							DATE_FORMAT(Ucards.cdate, '%Y')='".$yearselected."' AND card_id =3
							AND Ucards.cdate IN (SELECT MAX(Ucards.cdate) from user_cards Ucards group by Ucards.user_id)
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards3 ON u.id = Ucards3.user_id
						WHERE u.skim >= 48 AND u.skim<=54
						ORDER BY u.id";
		$grade4854results = $connection->execute($grade4854sql)->fetchAll('assoc');
		$this->set('grade4854result',$grade4854results);
		
		$totallate4854sql = "SELECT count(DISTINCT(DAY(Attendances.cdate))) as total_late,Attendances.cdate,Attendances.user_id,
							Attendances.remarks
							FROM attendances Attendances 
							LEFT JOIN users u ON Attendances.user_id= u.id
							WHERE MONTH(Attendances.cdate)= '".$monthselected."'
							AND YEAR(Attendances.cdate)= '".$yearselected."'
							AND DATE_FORMAT(Attendances.cdate, '%H:%i:%s')>='09:00:00'
							AND Attendances.status = 1 
							AND u.skim >= 48 AND u.skim<=54
							GROUP BY Attendances.user_id";
		$totallate4854results = $connection->execute($totallate4854sql)->fetchAll('assoc');
		$this->set('totallate4854result',$totallate4854results);
		
		//result for grade 41 to 44
		$grade4144sql = "SELECT count(u.id) as total_officer,count(Ucards.green) as green,count(Ucards2.yellow) as yellow,
						count(Ucards3.red) as red
						FROM users u
						LEFT JOIN (
							SELECT card_id AS green,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND
							DATE_FORMAT(Ucards.cdate, '%Y')='".$yearselected."' AND card_id =1
							AND Ucards.cdate IN (SELECT MAX(Ucards.cdate) from user_cards Ucards group by Ucards.user_id)
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards ON u.id = Ucards.user_id
						LEFT JOIN (
							SELECT card_id as yellow,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND
							DATE_FORMAT(Ucards.cdate, '%Y')='".$yearselected."' AND card_id =2
							AND Ucards.cdate IN (SELECT MAX(Ucards.cdate) from user_cards Ucards group by Ucards.user_id)
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC	
						)Ucards2 ON u.id = Ucards2.user_id
						LEFT JOIN (
							SELECT card_id as red,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND
							DATE_FORMAT(Ucards.cdate, '%Y')='".$yearselected."' AND card_id =3
							AND Ucards.cdate IN (SELECT MAX(Ucards.cdate) from user_cards Ucards group by Ucards.user_id)
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards3 ON u.id = Ucards3.user_id
						WHERE u.skim >= 41 AND u.skim<=44
						ORDER BY u.id";
		$grade4144results = $connection->execute($grade4144sql)->fetchAll('assoc');
		$this->set('grade4144result',$grade4144results);
		
		$totallate4144sql = "SELECT count(DISTINCT(DAY(Attendances.cdate))) as total_late,Attendances.cdate,Attendances.user_id,
							Attendances.remarks
							FROM attendances Attendances 
							LEFT JOIN users u ON Attendances.user_id= u.id
							WHERE MONTH(Attendances.cdate)= '".$monthselected."'
							AND YEAR(Attendances.cdate)= '".$yearselected."'
							AND DATE_FORMAT(Attendances.cdate, '%H:%i:%s')>='09:00:00'
							AND Attendances.status = 1 
							AND u.skim >= 41 AND u.skim<=44
							GROUP BY Attendances.user_id";
		$totallate4144results = $connection->execute($totallate4144sql)->fetchAll('assoc');
		$this->set('totallate4144result',$totallate4144results);
		
		//result for grade 17 to 40
		$grade1740sql = "SELECT count(u.id) as total_officer,count(Ucards.green) as green,count(Ucards2.yellow) as yellow,
						count(Ucards3.red) as red
						FROM users u
						LEFT JOIN (
							SELECT card_id AS green,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND 
							DATE_FORMAT(Ucards.cdate, '%Y')='".$yearselected."' AND card_id =1
							AND Ucards.cdate IN (SELECT MAX(Ucards.cdate) from user_cards Ucards group by Ucards.user_id)
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards ON u.id = Ucards.user_id
						LEFT JOIN (
							SELECT card_id as yellow,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND
							DATE_FORMAT(Ucards.cdate, '%Y')='".$yearselected."' AND card_id =2
							AND Ucards.cdate IN (SELECT MAX(Ucards.cdate) from user_cards Ucards group by Ucards.user_id)
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC	
						)Ucards2 ON u.id= Ucards2.user_id
						LEFT JOIN (
							SELECT card_id as red,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND
							DATE_FORMAT(Ucards.cdate, '%Y')='".$yearselected."' AND card_id =3
							AND Ucards.cdate IN (SELECT MAX(Ucards.cdate) from user_cards Ucards group by Ucards.user_id)
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards3 ON u.id = Ucards3.user_id
						WHERE u.skim >= 17 AND u.skim<=40
						ORDER BY u.id";
		$grade1740results = $connection->execute($grade1740sql)->fetchAll('assoc');
		$this->set('grade1740result',$grade1740results);
		
		$totallate1740sql = "SELECT count(DISTINCT(DAY(Attendances.cdate))) as total_late,Attendances.cdate,Attendances.user_id,
							Attendances.remarks
							FROM attendances Attendances 
							LEFT JOIN users u ON Attendances.user_id= u.id
							WHERE MONTH(Attendances.cdate)= '".$monthselected."'
							AND YEAR(Attendances.cdate)= '".$yearselected."'
							AND DATE_FORMAT(Attendances.cdate, '%H:%i:%s')>'09:00:00'
							AND Attendances.status = 1 
							AND u.skim >= 17 AND u.skim<=40
							GROUP BY Attendances.user_id";
		$totallate1740results = $connection->execute($totallate1740sql)->fetchAll('assoc');
		$this->set('totallate1740result',$totallate1740results);
		
		//result for grade 1 to 16
		$grade116sql = "SELECT count(u.id) as total_officer,count(Ucards.green) as green,count(Ucards2.yellow) as yellow,
						count(Ucards3.red) as red
						FROM users u
						LEFT JOIN (
							SELECT card_id AS green,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND
							DATE_FORMAT(Ucards.cdate, '%Y')='".$yearselected."' AND card_id =1
							AND Ucards.cdate IN (SELECT MAX(Ucards.cdate) from user_cards Ucards group by Ucards.user_id)
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards ON u.id = Ucards.user_id
						LEFT JOIN (
							SELECT card_id as yellow,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND
							DATE_FORMAT(Ucards.cdate, '%Y')='".$yearselected."' AND card_id =2
							AND Ucards.cdate IN (SELECT MAX(Ucards.cdate) from user_cards Ucards group by Ucards.user_id)
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC	
						)Ucards2 ON u.id = Ucards2.user_id
						LEFT JOIN (
							SELECT card_id as red,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND
							DATE_FORMAT(Ucards.cdate, '%Y')='".$yearselected."' AND card_id =3
							AND Ucards.cdate IN (SELECT MAX(Ucards.cdate) from user_cards Ucards group by Ucards.user_id)
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards3 ON u.id = Ucards3.user_id
						WHERE u.skim >= 1 AND u.skim<=16
						ORDER BY u.id";
		$grade116results = $connection->execute($grade116sql)->fetchAll('assoc');
		$this->set('grade116result',$grade116results);
		
		$totallate116sql = "SELECT count(DISTINCT(DAY(Attendances.cdate))) as total_late,Attendances.cdate,Attendances.user_id, 			
							Attendances.remarks
							FROM attendances Attendances 
							LEFT JOIN users u ON Attendances.user_id= u.id
							WHERE MONTH(Attendances.cdate)= '".$monthselected."'
							AND YEAR(Attendances.cdate)= '".$yearselected."'
							AND DATE_FORMAT(Attendances.cdate, '%H:%i:%s')>='09:00:00'
							AND Attendances.status = 1 
							AND u.skim >= 1 AND u.skim<=16
							GROUP BY Attendances.user_id";
		$totallate116results = $connection->execute($totallate116sql)->fetchAll('assoc');
		$this->set('totallate116result',$totallate116results);
		
		$this->viewBuilder()->options([
            'pdfConfig' => [
                'orientation' => 'landscape',
                'filename' => 'SummaryReport.pdf'
            ]
        ]);
		//connection to template
        $this->set(compact('grade55result','grade4854result','grade4144result','grade1740result','grade116result', 'totallate55result','totallate4854result','totallate4144result','totallate1740result','totallate1740sql','totallate116result','monthselected','totallate4854sql','userRoles','departments','users','departmentSelected','userSelected','yearselected'));
        $this->set('_serialize', ['report']);
    }
	public function exportExcelDaily()
    {
		$dateselected = $this->request->query['date_attendance'];
		$departmentSelected = $this->request->query['department'];
		$userSelected = $this->request->query['user'];
		$filterSelected = $this->request->query('filterby');
		
		
		$tempYear	= date("Y");
		$tempMonth	= date("m");
		$tempDay 	= date("d");
		
		if ($dateselected){
			$tempYear	=date("Y",strtotime($dateselected));
			$tempMonth	=date("m",strtotime($dateselected));
			$tempDay	=date("d",strtotime($dateselected));
		} else {
			$dateselected = date( 'Y-m-d');
		}
		
		$sql = "SELECT Users.*, Attendances.cdate as attn_date,Attendances.in_time,Attendances2.out_time, Attendances.remarks,UserCards.card_colour,Organization.name as organization_name,Attendances.late_status,Attendances.late_remark,uleave.reason
			FROM users Users
			LEFT JOIN (
				SELECT Attendances.cdate,Attendances.cdate as in_time,Attendances.status,Attendances.user_id,Attendances.remarks,attn_late.status as late_status, attn_late.late_remark
				FROM attendances Attendances 
				LEFT JOIN attendance_lates attn_late ON attn_late.attendance_id=Attendances.id
				WHERE date(Attendances.cdate) = '".$dateselected."' and Attendances.status=1
				GROUP BY Attendances.user_id
			)Attendances ON Users.id = Attendances.user_id
			LEFT JOIN (
				SELECT Attendances2.cdate as out_time,Attendances2.user_id
				FROM attendances Attendances2 
				WHERE date(Attendances2.cdate) = '".$dateselected."' and Attendances2.status=2
				GROUP BY Attendances2.user_id
			)Attendances2 ON Users.id = Attendances2.user_id
			LEFT JOIN (
				SELECT ucards.user_id,c.name as card_colour FROM user_cards ucards
				LEFT JOIN cards c ON ucards.card_id=c.id 
				WHERE date(ucards.cdate) = '".$dateselected."'
				GROUP BY ucards.user_id
			) UserCards ON UserCards.user_id = Users.id
			LEFT JOIN (
				SELECT uleave.reason, uleave.user_id
				FROM user_leaves uleave
				WHERE date(uleave.date_start) <= '".$dateselected."' 
				AND date(uleave.date_end) >= '".$dateselected."'
				AND uleave.status = 1
				GROUP BY uleave.user_id
			) uleave ON uleave.user_id = Users.id
			LEFT JOIN user_organizations Uorganization ON Uorganization.user_id = Users.id			
			LEFT JOIN organizations Organization ON Uorganization.organization_id = Organization.id
			WHERE Users.status = 1";
		
		if ($departmentSelected){
			$sql .= " AND Uorganization.organization_id = '".$departmentSelected."'";
		}
		if ($userSelected){
			$sql .= " AND Users.id = '".$userSelected."'";
		}
		$sql .= " ORDER BY Users.card_no";
		
		$connection = ConnectionManager::get('default');
		$results = $connection->execute($sql)->fetchAll('assoc');
		
		
		//start to export
		if ($departmentSelected){
			$outputdepartment = $results[0]['organization_name'];
		}else{
			$outputdepartment = __('All');
		}
		if ($userSelected){
			$outputuser = $results[0]['name'];
		}else{
			$outputuser = __('All');
		}
		
		$file_date=date('dMY');
		$file_fullname = $file_date.'_DailyReport';
		$now = \Cake\I18n\Time::now();
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="'.$file_fullname.'.csv"');
		$output= fopen('php://output', 'w');
		//output header
		fputcsv($output,array(__('Report Type'), __('Daily Reports')));
		
		fputcsv($output,array(__('Date'),$dateselected));
		fputcsv($output,array(__('Department'),$outputdepartment));
		fputcsv($output,array(__("Staff's Name"),$outputuser));
		fputcsv($output,array(''));
		
		//output column headings
		fputcsv($output, array(__('Bil'), __('Name'), __('Card No.'), __('In Time'), __('Out Time'), __('Time Off'), __('Late (With Approval)'), __('Total Hour')));
		$count_no=1;

		$totalyellow = 0;
		$totalred = 0;
		$totalgreen = 0;
		
		foreach ($results as $key => $user){
			$totalhours = '';
			$intime = '';
			$outtime = '';
			
			$result = explode("||",$user['attn_time']);
			$diff = strtotime($user['out_time']) - strtotime($user['in_time']);
			$hours = $diff / ( 60 * 60 );
			
			$showData = 1;
			if($filterSelected == 1){
				$showData = 0;
				if(date('H:i:s',strtotime($user['in_time']))	 > date('H:i:s',strtotime('09:00:00'))){
					$showData = 1;
				}
			}if($filterSelected == 2){
				$showData = 0;
				if($user['attn_date'] != '' && $hours < '9'){
					$showData = 1;
				}
			}if($filterSelected == 3){
				$showData = 0;
				if($user['attn_date'] == ''){
					$showData = 1;
				}
			}
			
			if ($user['late_status'] != NULL){
				$late_status_approval = __('yes');
				if($user['late_status'] == '2' || $user['late_status']== '0'){
					$late_status_approval = __('no');
				}
			} else {
				$late_status_approval = '';
			}
			
			if ($showData == 1){
				if ($user['card_colour'] == 'Yellow'){ $totalyellow += 1;}
				if ($user['card_colour'] == 'Green'){ $totalgreen += 1;}
				if ($user['card_colour'] == 'Red'){ $totalred += 1;}
				if($hours>0){ $totalhours = round($hours, 2);}
				if ($user['in_time'] !=''){ $intime = date('H:i:s',strtotime($user['in_time']));}
				if ($user['out_time'] !=''){ $outtime = date('H:i:s',strtotime($user['out_time']));}
				
				$data[]=$count_no .','.$user['name'] .','.$user['card_no'].','.$intime.','.$outtime.','.$user['reason'].','.$late_status_approval.','.$totalhours;
				$count_no++;	
			}
		}	
		$total_officer = $count_no - 1;
		$data[]=',,,,,'.__('Total Officer').','.$total_officer;		
		$count_no++;
		$data[]=',,,,,'.__('Total Officer That Hold Red Cards').','.$totalred;	
		$count_no++;		
		$data[]=',,,,,'.__('Total Officer That Hold Green Cards').','.$totalgreen;		
		$count_no++;
		
		$size=count($data);
		$count=0;
		
		while($count<$size){
			fputcsv($output, explode(',', $data[$count]));
			$count++;
		}
		
			
		
		fclose($output); die();
	}
	public function exportExcelWeekly()
    {
		$dateselected = $this->request->query['date_attendance'];
		$departmentSelected = $this->request->query('department');
        $userSelected = $this->request->query('user');
		
		$thisweekStart	= date('Y-m-d', strtotime( 'sunday last week' ) );
		$thisweekEnd	= date( 'Y-m-d', strtotime( 'saturday this week' ) );
		
		if ($dateselected){
			$thisweekStart	= date('Y-m-d', strtotime( 'sunday last week',strtotime($dateselected)));
			$thisweekEnd	= date('Y-m-d', strtotime( 'saturday this week',strtotime($dateselected)));
		}else {
			$dateselected = date( 'Y-m-d');
		}
		
		$weeklysql = "SELECT Users.*,Attendances.total_late,cardinfo.card_colour as card_colour, 
				Organization.name as 	organization_name ,Attendances.approved_late
				FROM users Users
				LEFT JOIN (
					SELECT count(Attendances.user_id) as total_late,Attendances.cdate,Attendances.user_id,
					count(attn_late.status=1) as approved_late
					FROM attendances Attendances 
					LEFT JOIN attendance_lates attn_late ON attn_late.attendance_id=Attendances.id
					WHERE  DATE_FORMAT(Attendances.cdate, '%Y-%m-%d')>='".$thisweekStart."'
					AND DATE_FORMAT(Attendances.cdate, '%Y-%m-%d')<='".$thisweekEnd."'
					AND DATE_FORMAT(Attendances.cdate, '%H:%i:%s')>='09:00:00'
					AND Attendances.status = 1 
					GROUP BY Attendances.user_id
				)Attendances ON Users.id = Attendances.user_id
				LEFT JOIN (
					SELECT Ucards.cdate,Ucards.card_id,Ucards.user_id,Ucards.remarks,Cards.name as card_colour
					from user_cards Ucards
					LEFT JOIN cards Cards ON Cards.id=Ucards.card_id 
					WHERE  DATE_FORMAT(Ucards.cdate, '%Y-%m-%d')>='".$thisweekStart."'
					AND DATE_FORMAT(Ucards.cdate, '%Y-%m-%d')<='".$thisweekEnd."'
					ORDER by cdate Desc
					LIMIT 18446744073709551615
				) cardinfo ON Users.id =cardinfo.user_id
				LEFT JOIN user_organizations Uorganization ON Uorganization.user_id = Users.id
				LEFT JOIN organizations Organization ON Uorganization.organization_id = Organization.id
				WHERE Users.status = 1";
		
		if ($departmentSelected){
			$weeklysql .= " AND Uorganization.organization_id = '".$departmentSelected."'";
		}
		if ($userSelected){
			$weeklysql .= " AND Users.id = '".$userSelected."'";			
		}
		
		$weeklysql .= " GROUP BY  Users.id
				ORDER BY Users.card_no";
		
		$connection = ConnectionManager::get('default');
		$weeklyresults = $connection->execute($weeklysql)->fetchAll('assoc');
		
		
		//start to export
		if ($departmentSelected){
			$outputdepartment = $weeklyresults[0]['organization_name'];
		}else{
			$outputdepartment = __('All');
		}
		if ($userSelected){
			$outputuser = $weeklyresults[0]['name'];
		}else{
			$outputuser = __('All');
		}
		
		$file_date=date('dMY');
		$file_fullname = $file_date.'_WeeklyReport';
		$now = \Cake\I18n\Time::now();
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="'.$file_fullname.'.csv"');
		$output= fopen('php://output', 'w');
		//output header
		fputcsv($output,array(__('Report Type'), __('Weekly Reports')));
		
		fputcsv($output,array(__('Date'),$thisweekStart.' To '.$thisweekEnd));
		fputcsv($output,array(__('Departments'),$outputdepartment));
		fputcsv($output,array(__("Staff's Name"),$outputuser));
		fputcsv($output,array(''));
		
		//output column headings
		fputcsv($output, array(__('Bil'), __('Name'),__( 'Card No.'), __('Total Late in a week with approval'), __('Total Late in a week without approval'), __('Card colour for end week')));
		$count_no=1;
		
		$totalyellow = 0;
		$totalred = 0;
		$totalgreen = 0;
		foreach ($weeklyresults as $key => $user){
			if ($user['card_colour'] == 'Yellow'){ $totalyellow += 1;}
			if ($user['card_colour'] == 'Green'){ $totalgreen += 1;}
			if ($user['card_colour'] == 'Red'){ $totalred += 1;}
			if($user['total_late'] >=3 ) {$totalLate = '1';} else { $totalLate =''; }
			$late_not_approved1 = $user['total_late'] - $user['approved_late'];
			if($late_not_approved1 > 0) { $late_not_approved = $late_not_approved1;}
			
			$data[]=$count_no .','.$user['name'] .','.$user['card_no'].','.$user['approved_late'].','.$late_not_approved.','.__($user['card_colour']);
			$count_no++;	
		}
		$data[]=',,,,,';
		$total_officer = $count_no - 1;
		$data[]=',,,'.__('Total Officer').','.$total_officer;		
		$count_no++;
		$data[]=',,,'.__('Total Officer That Hold Red Cards').','.$totalred;	
		$count_no++;		
		$data[]=',,,'.__('Total Officer That Hold Green Cards').','.$totalgreen;		
		$count_no++;
		
		$size=count($data);
		$count=0;
		
		while($count<$size){
			fputcsv($output, explode(',', $data[$count]));
			$count++;
		}
		
			
		
		fclose($output); die();
	}
	public function exportExcelMonthly()
    {
		$monthselected = $this->request->query['att_month'];
		$yearselected = $this->request->query['att_year'];
		$departmentSelected = $this->request->query('department');
        $userSelected = $this->request->query('user');
		
		if ($monthselected){
			$thismonthStart	=date("Y-".$monthselected."-01");
			$thismonthEnd	=date("Y-".$monthselected."-t");
		} else {
			$monthselected	= date('m');
		}
		if ($yearselected){
			$thismonthStart	=date($yearselected."-".$monthselected."-01");
			$thismonthEnd	=date($yearselected."-".$monthselected."-t");
		} else {
			$yearselected	= date('m');
		}
		$monthlysql = "SELECT Users.*,Attendances.total_late,cardinfo.card_colour as card_colour,
				cardinfo.remarks as card_remarks,cardinfo.cdate,Organization.name as organization_name,g.name as grade, Attendances.approved_late
				FROM users Users
				LEFT JOIN grades g ON g.id = Users.grade_id				
				LEFT JOIN user_organizations UserOrganization ON UserOrganization.user_id = Users.id				
				LEFT JOIN organizations Organization ON UserOrganization.organization_id = Organization.id
				LEFT JOIN (
					SELECT count(DISTINCT(DAY(Attendances.cdate))) as total_late,Attendances.cdate,Attendances.user_id,
					count(attn_late.status=1) as approved_late
					FROM attendances Attendances 
					LEFT JOIN attendance_lates attn_late ON attn_late.attendance_id=Attendances.id
					WHERE  DATE_FORMAT(Attendances.cdate, '%Y-%m-%d')>='".$thismonthStart."'
					AND DATE_FORMAT(Attendances.cdate, '%Y-%m-%d')<='".$thismonthEnd."'
					AND DATE_FORMAT(Attendances.cdate, '%H:%i:%s')>='09:00:00'
					AND Attendances.status = 1 
					GROUP BY Attendances.user_id
				)Attendances ON Users.id = Attendances.user_id
				LEFT JOIN ( 
					SELECT Ucards.cdate,Ucards.card_id,Ucards.user_id,Ucards.remarks,Cards.name as card_colour
					FROM user_cards Ucards
					LEFT JOIN cards Cards ON Cards.id=Ucards.card_id 
					WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."'
					AND DATE_FORMAT(Ucards.cdate, '%Y')='".$yearselected."'
					ORDER by cdate ASC
					LIMIT 18446744073709551615 
				)cardinfo ON cardinfo.user_id=Users.id 
				WHERE Users.status = 1";
		
		if ($departmentSelected){
			$monthlysql .= " AND UserOrganization.organization_id = '".$departmentSelected."'";
		}
		if ($userSelected){
			$monthlysql .= " AND Users.id = '".$userSelected."'";
		}
		$monthlysql .= " GROUP BY Users.id ORDER BY Users.card_no";
				
		
		$connection = ConnectionManager::get('default');
		$monthlyresults = $connection->execute($monthlysql)->fetchAll('assoc');
		
		
		//start to export
		if ($departmentSelected){
			$outputdepartment = $monthlyresults[0]['organization_name'];
		}else{
			$outputdepartment = __('All');
		}
		if ($userSelected){
			$outputuser = $monthlyresults[0]['name'];
		}else{
			$outputuser = __('All');
		}
		
		
		
		$file_date=date('dMY');
		$file_fullname = $file_date.'_MonthlyReport';
		$now = \Cake\I18n\Time::now();
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="'.$file_fullname.'.csv"');
		$output= fopen('php://output', 'w');
		//output header
		//fputcsv($output,array( $monthlysql));
		fputcsv($output,array(__('Report Type'), __('Monthly Reports')));
		
		fputcsv($output,array(__('Month'),$monthselected));
		fputcsv($output,array(__('Department'),$outputdepartment));
		fputcsv($output,array(__("Staff's Name"),$outputuser));
		fputcsv($output,array(''));
		
		//output column headings
		fputcsv($output, array(__('Bil'), __('Name'), __('Grade'), __('Card No.'), __('Total Late'), __('With Officer Approval'),__('Without Officer Approval'), __('Card Colour'), __('Remarks')));
		$count_no=1;
		
		$totalyellow = 0;
		$totalred = 0;
		$totalgreen = 0;
		$total3times = 0;
						
		foreach ($monthlyresults as $key => $user){
			if ($user['card_colour'] == 'Yellow'){ $totalyellow += 1;}
			if ($user['card_colour'] == 'Green'){ $totalgreen += 1;}
			if ($user['card_colour'] == 'Red'){ $totalred += 1;}	
			if ($user['total_late'] >= 3){$total3times += 1;}
			
			if ($user['total_late']>0){ $totalLate = $user['total_late']; }else{ $totalLate = '-';}
			if ($user['total_late']>0){ $officerApproval = __('yes'); }else{ $officerApproval = '-'; }
			$late_not_approved1 = $user['total_late'] - $user['approved_late'];			
			if($late_not_approved1 > 0) { $late_not_approved = $late_not_approved1;}
			
			$data[]=$count_no .','.$user['name'] .','.$user['grade'].$user['skim'] .','.$user['card_no'].','.$totalLate.','.$user['approved_late'].','.$late_not_approved.','. __($user['card_colour']).','.$user['card_remarks'];
			$count_no++;	
		}		
		$data[]=',,,,,,,,';
		$total_officer = $count_no - 1;
		$data[]=',,,,,,'.__('Total Officer').','.$total_officer;		
		$count_no++;
		$data[]=',,,,,,'.__('Total Officer Late More Than 3 Times').','.$total3times;	
		$count_no++;
		$data[]=',,,,,,'.__('Total Officer That Hold Red Cards').','.$totalred;	
		$count_no++;		
		$data[]=',,,,,,'.__('Total Officer That Hold Green Cards').','.$totalgreen;		
		$count_no++;
		
		$size=count($data);
		$count=0;
		
		while($count<$size){
			fputcsv($output, explode(',', $data[$count]));
			$count++;
		}
		
			
		
		fclose($output); die();
	}
	public function exportExcelSummary()
    {
		$connection = ConnectionManager::get('default');
	
        $monthselected = $this->request->query('att_month');
        $yearselected = $this->request->query('att_year');
		
		if ($monthselected){
		} else {
			$monthselected	= date('m');
		}
		if ($yearselected){
		} else {
			$yearselected	= date('Y');
		}
		
		//result for grade 55 and above
		$grade55sql = "SELECT count(u.id) as total_officer,count(Ucards.green) as green,count(Ucards2.yellow) as yellow,
						count(Ucards3.red) as red
						FROM users u
						LEFT JOIN (
							SELECT card_id AS green,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND 
							DATE_FORMAT(Ucards.cdate, '%Y')='".$yearselected."' AND card_id =1
							AND Ucards.cdate IN (SELECT MAX(Ucards.cdate) from user_cards Ucards group by Ucards.user_id)							
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards ON u.id = Ucards.user_id
						LEFT JOIN (
							SELECT card_id as yellow,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND
							DATE_FORMAT(Ucards.cdate, '%Y')='".$yearselected."' AND card_id =2
							AND Ucards.cdate IN (SELECT MAX(Ucards.cdate) from user_cards Ucards group by Ucards.user_id)
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC	
						)Ucards2 ON u.id = Ucards2.user_id
						LEFT JOIN (
							SELECT card_id as red,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND
							DATE_FORMAT(Ucards.cdate, '%Y')='".$yearselected."' AND card_id =3
							AND Ucards.cdate IN (SELECT MAX(Ucards.cdate) from user_cards Ucards group by Ucards.user_id)
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards3 ON u.id = Ucards3.user_id
						WHERE u.skim >= 55
						ORDER BY u.id";
		$grade55results = $connection->execute($grade55sql)->fetchAll('assoc');
		
		$totallate55sql = "SELECT count(DISTINCT(DAY(Attendances.cdate))) as total_late,Attendances.cdate,Attendances.user_id,
							Attendances.remarks
							FROM attendances Attendances 
							LEFT JOIN users u ON Attendances.user_id= u.id
							WHERE MONTH(Attendances.cdate)= '".$monthselected."'
							AND YEAR(Attendances.cdate)= '".$yearselected."'
							AND DATE_FORMAT(Attendances.cdate, '%H:%i:%s')>='09:00:00'
							AND Attendances.status = 1 
							AND u.skim >= 55 
							GROUP BY Attendances.user_id";
		$totallate55results = $connection->execute($totallate55sql)->fetchAll('assoc');
		
		//result for grade 48 to 54
		$grade4854sql = "SELECT count(u.id) as total_officer,count(Ucards.green) as green,count(Ucards2.yellow) as yellow,
						count(Ucards3.red) as red
						FROM users u
						LEFT JOIN (
							SELECT card_id AS green,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND
							DATE_FORMAT(Ucards.cdate, '%Y')='".$yearselected."' AND card_id =1
							AND Ucards.cdate IN (SELECT MAX(Ucards.cdate) from user_cards Ucards group by Ucards.user_id)
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards ON u.id = Ucards.user_id
						LEFT JOIN (
							SELECT card_id as yellow,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND
							DATE_FORMAT(Ucards.cdate, '%Y')='".$yearselected."' AND card_id =2
							AND Ucards.cdate IN (SELECT MAX(Ucards.cdate) from user_cards Ucards group by Ucards.user_id)
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC	
						)Ucards2 ON u.id = Ucards2.user_id
						LEFT JOIN (
							SELECT card_id as red,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND
							DATE_FORMAT(Ucards.cdate, '%Y')='".$yearselected."' AND card_id =3
							AND Ucards.cdate IN (SELECT MAX(Ucards.cdate) from user_cards Ucards group by Ucards.user_id)
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards3 ON u.id = Ucards3.user_id
						WHERE u.skim >= 48 AND u.skim<=54
						ORDER BY u.id";
		$grade4854results = $connection->execute($grade4854sql)->fetchAll('assoc');

		$totallate4854sql = "SELECT count(DISTINCT(DAY(Attendances.cdate))) as total_late,Attendances.cdate,Attendances.user_id,
							Attendances.remarks
							FROM attendances Attendances 
							LEFT JOIN users u ON Attendances.user_id= u.id
							WHERE MONTH(Attendances.cdate)= '".$monthselected."'
							AND YEAR(Attendances.cdate)= '".$yearselected."'
							AND DATE_FORMAT(Attendances.cdate, '%H:%i:%s')>='09:00:00'
							AND Attendances.status = 1 
							AND u.skim >= 48 AND u.skim<=54
							GROUP BY Attendances.user_id";
		$totallate4854results = $connection->execute($totallate4854sql)->fetchAll('assoc');
		
		//result for grade 41 to 44
		$grade4144sql = "SELECT count(u.id) as total_officer,count(Ucards.green) as green,count(Ucards2.yellow) as yellow,
						count(Ucards3.red) as red
						FROM users u
						LEFT JOIN (
							SELECT card_id AS green,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND
							DATE_FORMAT(Ucards.cdate, '%Y')='".$yearselected."' AND card_id =1
							AND Ucards.cdate IN (SELECT MAX(Ucards.cdate) from user_cards Ucards group by Ucards.user_id)
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards ON u.id = Ucards.user_id
						LEFT JOIN (
							SELECT card_id as yellow,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND
							DATE_FORMAT(Ucards.cdate, '%Y')='".$yearselected."' AND card_id =2
							AND Ucards.cdate IN (SELECT MAX(Ucards.cdate) from user_cards Ucards group by Ucards.user_id)
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC	
						)Ucards2 ON u.id = Ucards2.user_id
						LEFT JOIN (
							SELECT card_id as red,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND
							DATE_FORMAT(Ucards.cdate, '%Y')='".$yearselected."' AND card_id =3
							AND Ucards.cdate IN (SELECT MAX(Ucards.cdate) from user_cards Ucards group by Ucards.user_id)
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards3 ON u.id = Ucards3.user_id
						WHERE u.skim >= 41 AND u.skim<=44
						ORDER BY u.id";
		$grade4144results = $connection->execute($grade4144sql)->fetchAll('assoc');
		
		$totallate4144sql = "SELECT count(DISTINCT(DAY(Attendances.cdate))) as total_late,Attendances.cdate,Attendances.user_id,
							Attendances.remarks
							FROM attendances Attendances 
							LEFT JOIN users u ON Attendances.user_id= u.id
							WHERE MONTH(Attendances.cdate)= '".$monthselected."'
							AND YEAR(Attendances.cdate)= '".$yearselected."'
							AND DATE_FORMAT(Attendances.cdate, '%H:%i:%s')>='09:00:00'
							AND Attendances.status = 1 
							AND u.skim >= 41 AND u.skim<=44
							GROUP BY Attendances.user_id";
		$totallate4144results = $connection->execute($totallate4144sql)->fetchAll('assoc');
		
		//result for grade 17 to 40
		$grade1740sql = "SELECT count(u.id) as total_officer,count(Ucards.green) as green,count(Ucards2.yellow) as yellow,
						count(Ucards3.red) as red
						FROM users u
						LEFT JOIN (
							SELECT card_id AS green,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND
							DATE_FORMAT(Ucards.cdate, '%Y')='".$yearselected."' AND card_id =1
							AND Ucards.cdate IN (SELECT MAX(Ucards.cdate) from user_cards Ucards group by Ucards.user_id)
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards ON u.id = Ucards.user_id
						LEFT JOIN (
							SELECT card_id as yellow,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND
							DATE_FORMAT(Ucards.cdate, '%Y')='".$yearselected."' AND card_id =2
							AND Ucards.cdate IN (SELECT MAX(Ucards.cdate) from user_cards Ucards group by Ucards.user_id)
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC	
						)Ucards2 ON u.id = Ucards2.user_id
						LEFT JOIN (
							SELECT card_id as red,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND
							DATE_FORMAT(Ucards.cdate, '%Y')='".$yearselected."' AND card_id =3
							AND Ucards.cdate IN (SELECT MAX(Ucards.cdate) from user_cards Ucards group by Ucards.user_id)
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards3 ON u.id = Ucards3.user_id
						WHERE u.skim >= 17 AND u.skim<=40
						ORDER BY u.id";
		$grade1740results = $connection->execute($grade1740sql)->fetchAll('assoc');
		
		$totallate1740sql = "SELECT count(DISTINCT(DAY(Attendances.cdate))) as total_late,Attendances.cdate,Attendances.user_id,
							Attendances.remarks
							FROM attendances Attendances 
							LEFT JOIN users u ON Attendances.user_id= u.id
							WHERE MONTH(Attendances.cdate)= '".$monthselected."'
							AND  YEAR(Attendances.cdate)= '".$yearselected."'
							AND DATE_FORMAT(Attendances.cdate, '%H:%i:%s')>'09:00:00'
							AND Attendances.status = 1 
							AND u.skim >= 17 AND u.skim<=40
							GROUP BY Attendances.user_id";
		$totallate1740results = $connection->execute($totallate1740sql)->fetchAll('assoc');

		
		//result for grade 1 to 16
		$grade116sql = "SELECT count(u.id) as total_officer,count(Ucards.green) as green,count(Ucards2.yellow) as yellow,
						count(Ucards3.red) as red
						FROM users u
						LEFT JOIN (
							SELECT card_id AS green,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND
							DATE_FORMAT(Ucards.cdate, '%Y')='".$yearselected."' AND card_id =1
							AND Ucards.cdate IN (SELECT MAX(Ucards.cdate) from user_cards Ucards group by Ucards.user_id)
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards ON u.id = Ucards.user_id
						LEFT JOIN (
							SELECT card_id as yellow,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND
							DATE_FORMAT(Ucards.cdate, '%Y')='".$yearselected."' AND card_id =2
							AND Ucards.cdate IN (SELECT MAX(Ucards.cdate) from user_cards Ucards group by Ucards.user_id)
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC	
						)Ucards2 ON u.id = Ucards2.user_id
						LEFT JOIN (
							SELECT card_id as red,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND
							DATE_FORMAT(Ucards.cdate, '%Y')='".$yearselected."' AND card_id =3
							AND Ucards.cdate IN (SELECT MAX(Ucards.cdate) from user_cards Ucards group by Ucards.user_id)
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards3 ON u.id = Ucards3.user_id
						WHERE u.skim >= 1 AND u.skim<=16
						ORDER BY u.id";
		$grade116results = $connection->execute($grade116sql)->fetchAll('assoc');

		
		$totallate116sql = "SELECT count(DISTINCT(DAY(Attendances.cdate))) as total_late,Attendances.cdate,Attendances.user_id, 			
							Attendances.remarks
							FROM attendances Attendances 
							LEFT JOIN users u ON Attendances.user_id= u.id
							WHERE MONTH(Attendances.cdate)= '".$monthselected."'
							AND YEAR(Attendances.cdate)= '".$yearselected."'
							AND DATE_FORMAT(Attendances.cdate, '%H:%i:%s')>='09:00:00'
							AND Attendances.status = 1 
							AND u.skim >= 1 AND u.skim<=16
							GROUP BY Attendances.user_id";
		$totallate116results = $connection->execute($totallate116sql)->fetchAll('assoc');
		
		//start to export
		
		//calculation total late officer
		
		//for grade 55 and above
		$count55late = 0;
		foreach ($totallate55results as $key => $value){
			if ($value['total_late'] >= 3){
				$count55late ++;
			}
		}
		if ($count55late > 0){ $totalLateOfficer55 = $count55late; } else { $totalLateOfficer55 = 0;}
		
		//for colour card
		if ($grade55results[0]['yellow'] > 0){ 
			$totalStaffYellow55 = $grade55results[0]['yellow']; 
		}else{ 
			$totalStaffYellow55 = 0; 
		}
		if ($grade55results[0]['green'] > 0){ 
			$totalStaffGreen55 = $grade55results[0]['green']; 
		}else{ 
			$totalStaffGreen55 = 0; 
		}
		if ($grade55results[0]['red'] > 0){ 
			$totalStaffRed55 = $grade55results[0]['red']; 
		}else{ 
			$totalStaffRed55 = 0; 
		}
		
		
		//for grade 48 to 54
		$count4854late = 0;
		foreach ($totallate4854results as $key => $value){
			if ($value['total_late'] >= 3){
				$count4854late ++;
			}
		}
		if ($count4854late > 0){ $totalLateOfficer4854 = $count4854late; } else { $totalLateOfficer4854 = 0;}
		
		//for colour card
		if ($grade4854results[0]['yellow'] > 0){ 
			$totalStaffYellow4854 = $grade4854results[0]['yellow']; 
		}else{ 
			$totalStaffYellow4854 = 0; 
		}
		if ($grade4854results[0]['green'] > 0){ 
			$totalStaffGreen4854 = $grade4854results[0]['green']; 
		}else{ 
			$totalStaffGreen4854 = 0; 
		}
		if ($grade4854results[0]['red'] > 0){ 
			$totalStaffRed4854 = $grade4854results[0]['red']; 
		}else{ 
			$totalStaffRed4854 = 0; 
		}
		
		//for grade 41 to 44
		$count4144late = 0;
		foreach ($totallate4144results as $key => $value){
			if ($value['total_late'] >= 3){
				$count4144late ++;
			}
		}
		if ($count4144late > 0){ $totalLateOfficer4144 = $count4144late; } else { $totalLateOfficer4144 = 0;}
		
		//for colour card
		if ($grade4144results[0]['yellow'] > 0){ 
			$totalStaffYellow4144 = $grade4144results[0]['yellow']; 
		}else{ 
			$totalStaffYellow4144 = 0; 
		}
		if ($grade4144results[0]['green'] > 0){ 
			$totalStaffGreen4144 = $grade4144results[0]['green']; 
		}else{ 
			$totalStaffGreen4144 = 0; 
		}
		if ($grade4144results[0]['red'] > 0){ 
			$totalStaffRed4144 = $grade4144results[0]['red']; 
		}else{ 
			$totalStaffRed4144 = 0; 
		}
		
		//for grade 17 to 40
		$count1740late = 0;
		foreach ($totallate1740results as $key => $value){
			if ($value['total_late'] >= 3){
				$count1740late ++;
			}
		}
		if ($count1740late > 0){ $totalLateOfficer1740 = $count1740late; } else { $totalLateOfficer1740 = 0;}
		
		//for colour card
		if ($grade1740results[0]['yellow'] > 0){ 
			$totalStaffYellow1740 = $grade1740results[0]['yellow']; 
		}else{ 
			$totalStaffYellow1740 = 0; 
		}
		if ($grade1740results[0]['green'] > 0){ 
			$totalStaffGreen1740 = $grade1740results[0]['green']; 
		}else{ 
			$totalStaffGreen1740 = 0; 
		}
		if ($grade1740results[0]['red'] > 0){ 
			$totalStaffRed1740 = $grade1740results[0]['red']; 
		}else{ 
			$totalStaffRed1740 = 0; 
		}
		
		//for grade 1 to 16
		$count116late = 0;
		foreach ($totallate116results as $key => $value){
			if ($value['total_late'] >= 3){
				$count116late ++;
			}
		}
		if ($count116late > 0){ $totalLateOfficer116 = $count116late; } else { $totalLateOfficer116 = 0;}
		
		
		//for colour card
		if ($grade116results[0]['yellow'] > 0){ 
			$totalStaffYellow116 = $grade116results[0]['yellow']; 
		}else{ 
			$totalStaffYellow116 = 0; 
		}
		if ($grade116results[0]['green'] > 0){ 
			$totalStaffGreen116 = $grade116results[0]['green']; 
		}else{ 
			$totalStaffGreen116 = 0; 
		}
		if ($grade116results[0]['red'] > 0){ 
			$totalStaffRed116 = $grade116results[0]['red']; 
		}else{ 
			$totalStaffRed116 = 0; 
		}
		
		$file_date=date('dMY');
		$file_fullname = $file_date.'_SummaryReport';
		$now = \Cake\I18n\Time::now();
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="'.$file_fullname.'.csv"');
		$output= fopen('php://output', 'w');
		//output header
		fputcsv($output,array(__('Report Type'), __('Monthly Summary Reports')));
		
		fputcsv($output,array(__('Month'),$monthselected));
		fputcsv($output,array(''));
		
		
		//output column headings
		fputcsv($output, array(__('Bil'), __('Officer Group'), __('Total Officer'), '',__('Card Colour'),'' ,__('Three late in a month (total officer)'), __('Remarks')));
		fputcsv($output, array('', '', '', __('Yellow'), __('Green'),  __('Red'),''));
		
		$count_no=1;

		
		$data[]=$count_no .','.__('Higher Management Group').','.$grade55results[0]['total_officer'] .','.$totalStaffYellow55 .','.$totalStaffGreen55.','.$totalStaffRed55.','.$totalLateOfficer55.','.$count55late[0]['remarks'];
		$count_no++;
		$data[]=$count_no .','.__('Professional Management Group (Grade 48-54)').','.$grade4854results[0]['total_officer'] .','.$totalStaffYellow4854 .','.$totalStaffGreen4854.','.$totalStaffRed4854.','.$totalLateOfficer4854.','.$count4854late[0]['remarks'];
		$count_no++;
		$data[]=$count_no .','.__('Professional Management Group (Grade 41-44)').','.$grade4144results[0]['total_officer'] .','.$totalStaffYellow4144 .','.$totalStaffGreen4144.','.$totalStaffRed4144.','.$totalLateOfficer4144.','.$count4144late[0]['remarks'];
		$count_no++;
		$data[]=$count_no .','.__('Executing Group (Grade 17-40)').','.$grade1740results[0]['total_officer'] .','.$totalStaffYellow1740 .','.$totalStaffGreen1740.','.$totalStaffRed1740.','.$totalLateOfficer1740.','.$count1740late[0]['remarks'];
		$count_no++;
		$data[]=$count_no .','.__('Executing Group (Grade 1-16)').','.$grade116results[0]['total_officer'] .','.$totalStaffYellow116 .','.$totalStaffGreen116.','.$totalStaffRed116.','.$totalLateOfficer116.','.$count116late[0]['remarks'];
		$count_no++;	
			
		//grand total count
		$gtotalstaff= $grade55results[0]['total_officer'] + $grade4854results[0]['total_officer'] + $grade4144results[0]['total_officer'] + $grade1740results[0]['total_officer'] + $grade116results[0]['total_officer'];
									
		$gtotalyellow= $totalStaffYellow55 + $totalStaffYellow4854 +$totalStaffYellow4144 + $totalStaffYellow1740 +$totalStaffYellow116;
		
		$gtotalgreen= $totalStaffGreen55 + $totalStaffGreen4854 +$totalStaffGreen4144 + $totalStaffGreen1740 + $totalStaffGreen116;
		
		$gtotalred= $totalStaffRed55 + $totalStaffRed4854 +$totalStaffRed4144 + $totalStaffRed1740 +$totalStaffRed116;
		
		$gtotal3times = $totalLateOfficer55 + $totalLateOfficer4854 + $totalLateOfficer4144 + $totalLateOfficer1740 + $totalLateOfficer116;
			
		$data[]=',,,,,,,';
		$data[]=','.__('Grand Total').','.$gtotalstaff.','.$gtotalyellow.','.$gtotalgreen.','.$gtotalred.','.$gtotal3times;
		$size=count($data);
		$count=0;
		
		while($count<$size){
			fputcsv($output, explode(',', $data[$count]));
			$count++;
		}
		
			
		
		fclose($output); die();
    }
	public function pdf(){
		App::import('Vendor', 'Fpdf', array('file' => 'fpdf/fpdf.php'));
		$this->layout = 'pdf'; //this will use the pdf.ctp layout
		$this->set('fpdf', new FPDF('P', 'mm', 'A4'));
		$data=$this->Post->find('first', array('conditions' => array('Post.id' => '14')));
		$this->set(compact('data'));
		$this->render('pdf');
    }
	
	 public function daily_tf()
    {
       
		$Users = TableRegistry::get('Users');
		$Users = TableRegistry::getTableLocator()->get('Users');
		$departments = TableRegistry::get('Organizations');
		$departments = TableRegistry::getTableLocator()->get('Organizations');
		$dateselected = $this->request->query['date_attendance'];
		$leaveTypeselected = $this->request->query['leaveType'];
		$departmentSelected = $this->request->query('department');
        $userSelected = $this->request->query('user');
        $filterSelected = $this->request->query('filterby');
		$userId = $this->AuthUser->id();
        $user = $this->Users->find()->contain(['Roles'])->Where(['id' => "$userId"])->limit(1)->first();
        $userRoles = $this->Users->Roles->initRolesChecker($user->roles);
		$users = $this->Users->find('list');
        $departments = $this->Organizations->find('list');
		
		//department session
		$sqldepartment = "SELECT * FROM user_organizations WHERE user_id='".$userId."'";
		$this->set('resultDepartment',$sqldepartment);
		$deptId = $resultDepartment[0]['organization_id'];
		
		if ($userRoles->hasRole(['Master Admin']) && $departmentSelected) {
		   $users = $this->Users->find('list')->order(['Users.name' => 'ASC'])->innerJoinWith('UserOrganizations.Organizations' , function($q) use($departmentSelected){
            return $q->where(['UserOrganizations.organization_id'=>$departmentSelected])->where(['Users.status'=>1]);});
	    }
		if ($userRoles->hasRole(['Supervisor']) && $deptId) {
		   $users = $this->Users->find('list')->order(['Users.name' => 'ASC'])->innerJoinWith('UserOrganizations.Organizations' , function($q) use($departmentSelected){
            return $q->where(['UserOrganizations.organization_id'=>$deptId])->where(['Users.status'=>1]);});
	    }
		
		$tempYear	= date("Y");
		$tempMonth	= date("m");
		$tempDay 	= date("d");
		
		if ($dateselected){
			$tempYear	=date("Y",strtotime($dateselected));
			$tempMonth	=date("m",strtotime($dateselected));
			$tempDay	=date("d",strtotime($dateselected));
		} else {
			$dateselected = date( 'Y-m-d');
		}
		
		
		$dailytfsql = "SELECT ul.*,u.name as user_name,lt.name as leave_type,ls.name as leave_status,Organization.name as organization_name
				FROM user_leaves ul
				LEFT JOIN users u ON ul.user_id = u.id
				LEFT JOIN leave_types lt ON lt.id = ul.leave_type_id
				LEFT JOIN leave_status ls ON ls.id = ul.leave_status_id
				LEFT JOIN user_organizations Uorganization ON Uorganization.user_id = u.id			
				LEFT JOIN organizations Organization ON Uorganization.organization_id = Organization.id
				WHERE DATE_FORMAT(ul.date_start, '%Y-%m-%d') <= '".$dateselected."'
				AND DATE_FORMAT(ul.date_end, '%Y-%m-%d') >= '".$dateselected."'";
		
		if ($userRoles->hasRole(['Admin','Staff'])) {
			$userSelected 		= $userId;
			$departmentSelected = $deptId;
        }
		
		if ($departmentSelected){
			$dailytfsql .= " AND Uorganization.organization_id = '".$departmentSelected."'";
		}
		if ($userSelected){
			$dailytfsql .= " AND u.id = '".$userSelected."'";
		}
		if ($leaveTypeselected){
			$dailytfsql .= " AND lt.id = '".$leaveTypeselected."'";
		} 
		$dailytfsql .= " ORDER BY u.card_no";
		
		$connection = ConnectionManager::get('default');
		$dailytfresults = $connection->execute($dailytfsql)->fetchAll('assoc');
		
		$this->viewBuilder()->options([
            'pdfConfig' => [
                'orientation' => 'landscape',
                'filename' => 'DailyTimeOffReport.pdf'
            ]
        ]);

		$this->set('result',$dailytfresults);
        $this->set(compact('result','sql','dateselected', 'userRoles','departments','users','filterSelected','departmentSelected','userSelected','leaveTypeselected'));
		$this->set('_serialize', ['report']);
    }
	public function exportExcelDailytf()
    {
		$dateselected = $this->request->query['date_attendance'];
		$departmentSelected = $this->request->query['department'];
		$userSelected = $this->request->query['user'];
		$leaveTypeselected = $this->request->query('leaveType');
		
		if ($dateselected){
			
		} else {
			$dateselected = date( 'Y-m-d');
		}
		
		$dailytfsql = "SELECT ul.*,u.name as user_name,lt.name as leave_type,ls.name as leave_status,Organization.name as organization_name
				FROM user_leaves ul
				LEFT JOIN users u ON ul.user_id = u.id
				LEFT JOIN leave_types lt ON lt.id = ul.leave_type_id
				LEFT JOIN leave_status ls ON ls.id = ul.status
				LEFT JOIN user_organizations Uorganization ON Uorganization.user_id = u.id			
				LEFT JOIN organizations Organization ON Uorganization.organization_id = Organization.id
				WHERE DATE_FORMAT(ul.date_start, '%Y-%m-%d') <= '".$dateselected."'
				AND DATE_FORMAT(ul.date_end, '%Y-%m-%d') >= '".$dateselected."'";
				
		if ($departmentSelected){
			$dailytfsql .= " AND Uorganization.organization_id = '".$departmentSelected."'";
		}
		if ($userSelected){
			$dailytfsql .= " AND u.id = '".$userSelected."'";
		}
		if ($leaveTypeselected){
			$dailytfsql .= " AND lt.id = '".$leaveTypeselected."'";
		} 
		$dailytfsql .= " ORDER BY u.card_no";
		
		$connection = ConnectionManager::get('default');
		$results = $connection->execute($dailytfsql)->fetchAll('assoc');
		
		
		//start to export
		if ($departmentSelected){
			$outputdepartment = $results[0]['organization_name'];
		}else{
			$outputdepartment = __('All');
		}
		if ($userSelected){
			$outputuser = $results[0]['user_name'];
		}else{
			$outputuser = __('All');
		}
		if ($leaveTypeselected){
			$outputleavetype = $results[0]['leave_type'];
		}else{
			$outputleavetype = __('All');
		}
		
		$file_date=date('dMY');
		$file_fullname = $file_date.'_DailyTimeOffReport';
		$now = \Cake\I18n\Time::now();
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="'.$file_fullname.'.csv"');
		$output= fopen('php://output', 'w');
		//output header
		fputcsv($output,array(__('Report Type'), __('Daily Time Off')));
		
		fputcsv($output,array(__('Date'),$dateselected));
		fputcsv($output,array(__('Department'),$outputdepartment));
		fputcsv($output,array(__("Staff's Name"),$outputuser));
		fputcsv($output,array(__('Leave Type'),$outputleavetype));
		fputcsv($output,array(''));
		
		//output column headings
		fputcsv($output, array(__('Bil'), __('Name'), __('Leave Type'), __('Leave Date'), __('Leave Time'), __('Leave Status'), __('Reason')));
		$count_no=1;

		$totalyellow = 0;
		$totalred = 0;
		$totalgreen = 0;
		
		foreach ($results as $key => $user){
			$leaveTime = __('Start Time : ').$user['start_time'].' || '.__('End Time : ').$user['end_time'];
			$leaveDate = date('Y-m-d',strtotime($user['date_start'])).' To '.date('Y-m-d',strtotime($user['date_end']));
			
			$data[]=$count_no .','.$user['user_name'] .','.$user['leave_type'].','.$leaveDate.','.$leaveTime.','.$user['leave_status'].','.$user['reason'];
			$count_no++;	

		}	
		$size=count($data);
		$count=0;
		
		while($count<$size){
			fputcsv($output, explode(',', $data[$count]));
			$count++;
		}
		
			
		
		fclose($output); die();
	}
	public function staff_tf()
    {
       
		$Users = TableRegistry::get('Users');
		$Users = TableRegistry::getTableLocator()->get('Users');
		$departments = TableRegistry::get('Organizations');
		$departments = TableRegistry::getTableLocator()->get('Organizations');
		$leaveTypeselected = $this->request->query['leaveType'];
		$departmentSelected = $this->request->query('department');
        $userSelected = $this->request->query('user');
		$monthselected = $this->request->query('att_month');
		$yearselected = $this->request->query('att_year');
        $filterSelected = $this->request->query('filterby');
		$userId = $this->AuthUser->id();
        $user = $this->Users->find()->contain(['Roles'])->Where(['id' => "$userId"])->limit(1)->first();
        $userRoles = $this->Users->Roles->initRolesChecker($user->roles);
		$users = $this->Users->find('list');
        $departments = $this->Organizations->find('list');
		$connection = ConnectionManager::get('default');
		
		//department session
		$sqldepartment = "SELECT * FROM user_organizations WHERE user_id='".$userId."'";
		$resultDepartments = $connection->execute($sqldepartment)->fetchAll('assoc');
		
		$deptId = $resultDepartments[0]['organization_id'];
		
		if ($userRoles->hasRole(['Master Admin']) && $departmentSelected) {
		   $users = $this->Users->find('list')->order(['Users.name' => 'ASC'])->innerJoinWith('UserOrganizations.Organizations' , function($q) use($departmentSelected){
            return $q->where(['UserOrganizations.organization_id'=>$departmentSelected])->where(['Users.status'=>1]);});
	    }
		if ($userRoles->hasRole(['Supervisor']) && $deptId) {
		   $users = $this->Users->find('list')->order(['Users.name' => 'ASC'])->innerJoinWith('UserOrganizations.Organizations' , function($q) use($departmentSelected){
            return $q->where(['UserOrganizations.organization_id'=>$deptId])->where(['Users.status'=>1]);});
	    }
		
		//month
		if ($yearselected){
			$thismonthStart	=date($yearselected."-m-1");
			$thismonthEnd	=date($yearselected."-m-t");
		} else {
			$yearselected	= date('Y');
		}
		if ($monthselected){
			$thismonthStart	=date($yearselected."-".$monthselected."-01");
			$thismonthEnd	=date($yearselected."-".$monthselected."-t");
		} 
		
		
		$stafftfsql = "SELECT ul.*,u.name as user_name,lt.name as leave_type,ls.name as leave_status,Organization.name as organization_name
				FROM user_leaves ul
				LEFT JOIN users u ON ul.user_id = u.id
				LEFT JOIN leave_types lt ON lt.id = ul.leave_type_id
				LEFT JOIN leave_status ls ON ls.id = ul.leave_status_id
				LEFT JOIN user_organizations Uorganization ON Uorganization.user_id = u.id			
				LEFT JOIN organizations Organization ON Uorganization.organization_id = Organization.id
				WHERE u.status = 1";
		
		if ($userRoles->hasRole(['Admin','Staff'])) {
			$userSelected 		= $userId;
			$departmentSelected = $deptId;
        }
		
		if($monthselected){
			$stafftfsql .= " AND (MONTH(ul.date_start) = '".$monthselected."'
							OR MONTH(ul.date_end) = '".$monthselected."')";
		}
		if($yearselected){
			$stafftfsql .= " AND YEAR(ul.date_start) = '".$yearselected."'";
		}
		
		if ($departmentSelected && $userSelected!=$userId){
			$stafftfsql .= " AND Uorganization.organization_id = '".$departmentSelected."'";
		} else if ($userSelected!=$userId) {
			$stafftfsql .= " AND Uorganization.organization_id = '".$deptId."'";
			$departmentSelected = $deptId;			
		} else {
			$departmentSelected = $deptId;
		}
		if ($userSelected){
			$stafftfsql .= " AND u.id = '".$userSelected."'";
		} else {
			$stafftfsql .= " AND u.id = '".$userId."'";
			$userSelected = $userId;
		}
		if ($leaveTypeselected){
			$stafftfsql .= " AND lt.id = '".$leaveTypeselected."'";
		} 
		$stafftfsql .= " ORDER BY u.card_no";
		
		
		$stafftfresults = $connection->execute($stafftfsql)->fetchAll('assoc');
		
		$this->viewBuilder()->options([
            'pdfConfig' => [
                'orientation' => 'landscape',
                'filename' => 'DailyTimeOffReport.pdf'
            ]
        ]);

		$this->set('result',$stafftfresults);
        $this->set(compact('result','stafftfsql','sqldepartment','resultDepartment', 'userRoles','departments','users','filterSelected','departmentSelected','monthselected','userSelected','leaveTypeselected','yearselected'));
		$this->set('_serialize', ['report']);
    }
	public function exportExcelStafftf()
    {
		$departmentSelected = $this->request->query['department'];
		$userSelected = $this->request->query['user'];
		$leaveTypeselected = $this->request->query('leaveType');
		$monthselected = $this->request->query('att_month');
		$yearselected = $this->request->query('att_year');
		
		if ($dateselected){			
		} else {
			$dateselected = date( 'Y-m-d');
		}
		
		$stafftfsql = "SELECT ul.*,u.name as user_name,lt.name as leave_type,ls.name as leave_status,Organization.name as organization_name
				FROM user_leaves ul
				LEFT JOIN users u ON ul.user_id = u.id
				LEFT JOIN leave_types lt ON lt.id = ul.leave_type_id
				LEFT JOIN leave_status ls ON ls.id = ul.leave_status_id
				LEFT JOIN user_organizations Uorganization ON Uorganization.user_id = u.id			
				LEFT JOIN organizations Organization ON Uorganization.organization_id = Organization.id
				WHERE u.status = 1";
				
		if ($departmentSelected){
			$stafftfsql .= " AND Uorganization.organization_id = '".$departmentSelected."'";
		}
		if ($userSelected){
			$stafftfsql .= " AND u.id = '".$userSelected."'";
		}
		if ($leaveTypeselected){
			$stafftfsql .= " AND lt.id = '".$leaveTypeselected."'";
		} 
		if($monthselected){
			$stafftfsql .= " AND (MONTH(ul.date_start) = '".$monthselected."'
							OR MONTH(ul.date_end) = '".$monthselected."')";
		}
		if($yearselected){
			$stafftfsql .= " AND YEAR(ul.date_start) = '".$yearselected."'";
		}
		$stafftfsql .= " ORDER BY u.card_no";
		
		$connection = ConnectionManager::get('default');
		$results = $connection->execute($stafftfsql)->fetchAll('assoc');
		
		
		//start to export
		if ($departmentSelected){
			$outputdepartment = $results[0]['organization_name'];
		}else{
			$outputdepartment = __('All');
		}
		if ($userSelected){
			$outputuser = $results[0]['user_name'];
		}else{
			$outputuser = __('All');
		}
		if ($leaveTypeselected){
			$outputleavetype = $results[0]['leave_type'];
		}else{
			$outputleavetype = __('All');
		}
		if ($monthselected){
			$outputmonth = $monthselected;
		}else{
			$outputmonth = __('All');
		}
		
		$file_date=date('dMY');
		$file_fullname = $file_date.'_StaffTimeOffReport';
		$now = \Cake\I18n\Time::now();
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="'.$file_fullname.'.csv"');
		$output= fopen('php://output', 'w');
		//output header
		fputcsv($output,array(__('Report Type'), __('Staff Time Off')));
		
		fputcsv($output,array(__('Department'),$outputdepartment));
		fputcsv($output,array(__("Staff's Name"),$outputuser));
		fputcsv($output,array(__('Month'),$outputmonth));
		fputcsv($output,array(__('Leave Type'),$outputleavetype));
		fputcsv($output,array(''));
		
		//output column headings
		fputcsv($output, array(__('Bil'),  __('Leave Type'), __('Leave Date'), __('Leave Time'), __('Leave Status'), __('Reason'), __('Total Hour')));
		$count_no=1;

		$totalyellow = 0;
		$totalred = 0;
		$totalgreen = 0;
		
		foreach ($results as $key => $user){
			$leaveTime = __('Start Time : ').$user['start_time'].' || '.__('End Time : ').$user['end_time'];
			$leaveDate = date('Y-m-d',strtotime($user['date_start'])).' To '.date('Y-m-d',strtotime($user['date_end']));
			
			//calculate total hour
			$dateend = $user['date_end'].' '.$user['end_time'].':00';
			$datestart = $user['date_start'].' '.$user['start_time'].':00';
			$dateDiff = strtotime($dateend)-strtotime($datestart);
			
			$totalhourOutput='';
			if($dateDiff >= 2592000){
				$M = floor($dateDiff/2592000);
				$totalhourOutput.= $M.__('Month').' ';
			}
			if($dateDiff >= 86400){
				$d = floor(($dateDiff%2592000)/86400);
				$totalhourOutput.= $d.__('Day').' ';
			}
			if($dateDiff >= 3600){
				$h = floor(($dateDiff%86400)/3600);
				$totalhourOutput.= $h.__('Hour').' ';
			}
			if($dateDiff >= 60){
				$m = floor(($dateDiff%3600)/60);
				$totalhourOutput.= $m.__('Minute').' ';
			}
			
			$grandTotaldateDiff += $dateDiff;
			
			
			$data[]=$count_no .','.$user['leave_type'].','.$leaveDate.','.$leaveTime.','.$user['leave_status'].','.$user['reason'].','.$totalhourOutput;
			$count_no++;	

		}
		if($grandTotaldateDiff >= 2592000){
			$M = floor($grandTotaldateDiff/2592000);
			$gtotalhourOutput.= $M.__('Month').' ';
		}
		if($grandTotaldateDiff >= 86400){
			$d = floor(($grandTotaldateDiff%2592000)/86400);
			$gtotalhourOutput.= $d.__('Day').' ';
		}
		if($grandTotaldateDiff >= 3600){
			$h = floor(($grandTotaldateDiff%86400)/3600);
			$gtotalhourOutput.= $h.__('Hour').' ';
		}
		if($grandTotaldateDiff >= 60){
			$m = floor(($grandTotaldateDiff%3600)/60);
			$gtotalhourOutput.= $m.__('Minute').' ';
		}		
		$data[]=',,,,,'.__('Grand Total').','.$gtotalhourOutput;
		$count_no++;	

		
		$size=count($data);
		$count=0;
		
		while($count<$size){
			fputcsv($output, explode(',', $data[$count]));
			$count++;
		}
		
			
		
		fclose($output); die();
	}
	public function late_in()
    {
       
		$Users = TableRegistry::get('Users');
		$Users = TableRegistry::getTableLocator()->get('Users');
		$departments = TableRegistry::get('Organizations');
		$departments = TableRegistry::getTableLocator()->get('Organizations');
		
		$leaveTypeselected = $this->request->query['leaveType'];
		$departmentSelected = $this->request->query('department');
        $userSelected = $this->request->query('user');
		$monthselected = $this->request->query('att_month');
		$yearselected = $this->request->query('att_year');
		
		$userId = $this->AuthUser->id();
        $user = $this->Users->find()->contain(['Roles'])->Where(['id' => "$userId"])->limit(1)->first();
        $userRoles = $this->Users->Roles->initRolesChecker($user->roles);
		$users = $this->Users->find('list');
        $departments = $this->Organizations->find('list');
		$connection = ConnectionManager::get('default');
		
		//department session
		$sqldepartment = "SELECT * FROM user_organizations WHERE user_id='".$userId."'";
		$resultDepartments = $connection->execute($sqldepartment)->fetchAll('assoc');
		
		$deptId = $resultDepartments[0]['organization_id'];
		
		if ($userRoles->hasRole(['Master Admin']) && $departmentSelected) {
		   $users = $this->Users->find('list')->order(['Users.name' => 'ASC'])->innerJoinWith('UserOrganizations.Organizations' , function($q) use($departmentSelected){
            return $q->where(['UserOrganizations.organization_id'=>$departmentSelected])->where(['Users.status'=>1]);});
	    }
		if ($userRoles->hasRole(['Supervisor']) && $deptId) {
		   $users = $this->Users->find('list')->order(['Users.name' => 'ASC'])->innerJoinWith('UserOrganizations.Organizations' , function($q) use($departmentSelected){
            return $q->where(['UserOrganizations.organization_id'=>$deptId])->where(['Users.status'=>1]);});
	    }
		
		//month
		if ($yearselected){
			$thismonthStart	=date($yearselected."-m-1");
			$thismonthEnd	=date($yearselected."-m-t");
		} else {
			$yearselected	= date('Y');
		}
		if ($monthselected){
			$thismonthStart	=date($yearselected."-".$monthselected."-01");
			$thismonthEnd	=date($yearselected."-".$monthselected."-t");
		} 
		
		$lateinsql = "SELECT attn.*,u.name as user_name,Organization.name as organization_name
				FROM attendances attn
				LEFT JOIN users u ON attn.user_id = u.id
				LEFT JOIN user_organizations Uorganization ON Uorganization.user_id = u.id			
				LEFT JOIN organizations Organization ON Uorganization.organization_id = Organization.id
				WHERE u.status = 1 AND attn.status=1 AND DATE_FORMAT(attn.cdate, '%H:%i:%s')>='09:00:00'";
		
		if ($userRoles->hasRole(['Admin','Staff'])) {
			$userSelected 		= $userId;
			$departmentSelected = $deptId;
        }
		
		if($monthselected){
			$lateinsql .= " AND MONTH(attn.cdate) = '".$monthselected."'";
		}
		if($yearselected){
			$lateinsql .= " AND YEAR(attn.cdate) = '".$yearselected."'";
		}
		
		if ($departmentSelected){
			$lateinsql .= " AND Uorganization.organization_id = '".$departmentSelected."'";
		} 
	
		if ($userSelected){
			$lateinsql .= " AND u.id = '".$userSelected."'";
		}
		$lateinsql .= " GROUP BY u.id,DAY(attn.cdate)  ORDER BY u.card_no ";
		
		
		$stafftfresults = $connection->execute($lateinsql)->fetchAll('assoc');
		
		$this->viewBuilder()->options([
            'pdfConfig' => [
                'orientation' => 'landscape',
                'filename' => 'StaffLateInReport.pdf'
            ]
        ]);

		$this->set('result',$stafftfresults);
        $this->set(compact('result','lateinsql','sqldepartment','resultDepartment', 'userRoles','departments','users','filterSelected','departmentSelected','monthselected','userSelected','leaveTypeselected','yearselected'));
		$this->set('_serialize', ['report']);
    }	
	public function exportExcelLatein()
    {
		$departmentSelected = $this->request->query['department'];
		$userSelected = $this->request->query['user'];
		$monthselected = $this->request->query('att_month');
		$yearselected = $this->request->query('att_year');
		
		if ($dateselected){
			
		} else {
			$dateselected = date( 'Y-m-d');
		}
		
		$lateinsql = "SELECT attn.*,u.name as user_name,Organization.name as organization_name
				FROM attendances attn
				LEFT JOIN users u ON attn.user_id = u.id
				LEFT JOIN user_organizations Uorganization ON Uorganization.user_id = u.id			
				LEFT JOIN organizations Organization ON Uorganization.organization_id = Organization.id
				WHERE u.status = 1 AND attn.status=1 AND DATE_FORMAT(attn.cdate, '%H:%i:%s')>='09:00:00'";
		if($monthselected){
			$lateinsql .= " AND MONTH(attn.cdate) = '".$monthselected."'";
		}if($yearselected){
			$lateinsql .= " AND YEAR(attn.cdate) = '".$yearselected."'";
		}
		
		if ($departmentSelected){
			$lateinsql .= " AND Uorganization.organization_id = '".$departmentSelected."'";
		} 
	
		if ($userSelected){
			$lateinsql .= " AND u.id = '".$userSelected."'";
		}
		$lateinsql .= " GROUP BY u.id,DAY(attn.cdate) ORDER BY u.card_no";
		
		
		$connection = ConnectionManager::get('default');
		$results = $connection->execute($lateinsql)->fetchAll('assoc');
		
		
		//start to export
		if ($departmentSelected){
			$outputdepartment = $results[0]['organization_name'];
		}else{
			$outputdepartment = __('All');
		}
		if ($userSelected){
			$outputuser = $results[0]['user_name'];
		}else{
			$outputuser = __('All');
		}
		if ($monthselected){
			$outputmonth = $monthselected;
		}else{
			$outputmonth = __('All');
		}
		
		$file_date=date('dMY');
		$file_fullname = $file_date.'_LateInReport';
		$now = \Cake\I18n\Time::now();
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="'.$file_fullname.'.csv"');
		$output= fopen('php://output', 'w');
		//output header
		fputcsv($output,array(__('Report Type'), __('Late In Report')));
		
		fputcsv($output,array(__('Department'),$outputdepartment));
		fputcsv($output,array(__("Staff's Name"),$outputuser));
		fputcsv($output,array(__('Month'),$outputmonth));
		fputcsv($output,array(''));
		
		//output column headings
		fputcsv($output, array(__('Bil'), __('Name'), __('Date'), __('In Time')));
		$count_no=1;

		
		foreach ($results as $key => $user){			
			
			$data[]=$count_no .','.$user['user_name'].','.date('Y-m-d',strtotime($user['cdate'])).','. date('H:i:s',strtotime($user['cdate']));
			$count_no++;	

		}
		
		$size=count($data);
		$count=0;
		
		while($count<$size){
			fputcsv($output, explode(',', $data[$count]));
			$count++;
		}
		
			
		
		fclose($output); die();
	}
	public function working_hour()
    {
       
		$Users = TableRegistry::get('Users');
		$Users = TableRegistry::getTableLocator()->get('Users');
		$departments = TableRegistry::get('Organizations');
		$departments = TableRegistry::getTableLocator()->get('Organizations');
		
		$departmentSelected = $this->request->query('department');
        $userSelected = $this->request->query('user');
		$monthselected = $this->request->query('att_month');		
		$yearselected = $this->request->query('att_year');		
        $filterSelected = $this->request->query('filterby');
		
		$userId = $this->AuthUser->id();
        $user = $this->Users->find()->contain(['Roles'])->Where(['id' => "$userId"])->limit(1)->first();
        $userRoles = $this->Users->Roles->initRolesChecker($user->roles);
		$users = $this->Users->find('list');
        $departments = $this->Organizations->find('list');
		$connection = ConnectionManager::get('default');
		
		//department session
		$sqldepartment = "SELECT * FROM user_organizations WHERE user_id='".$userId."'";
		$resultDepartments = $connection->execute($sqldepartment)->fetchAll('assoc');
		
		$deptId = $resultDepartments[0]['organization_id'];
		
		if ($userRoles->hasRole(['Master Admin']) && $departmentSelected) {
		   $users = $this->Users->find('list')->order(['Users.name' => 'ASC'])->innerJoinWith('UserOrganizations.Organizations' , function($q) use($departmentSelected){
            return $q->where(['UserOrganizations.organization_id'=>$departmentSelected])->where(['Users.status'=>1]);});
	    }
		if ($userRoles->hasRole(['Supervisor']) && $deptId) {
		   $users = $this->Users->find('list')->order(['Users.name' => 'ASC'])->innerJoinWith('UserOrganizations.Organizations' , function($q) use($departmentSelected){
            return $q->where(['UserOrganizations.organization_id'=>$deptId])->where(['Users.status'=>1]);});
	    }
		
		//month
		if ($yearselected){
			$thismonthStart	=date($yearselected."-m-1");
			$thismonthEnd	=date($yearselected."-m-t");
		} else {
			$yearselected	= date('Y');
		}
		if ($monthselected){
			$lastDateMonth = date($yearselected.'-'.$monthselected.'-t');
			$lastDayMonth = date('t',strtotime($lastDateMonth));
		} else {
			$monthselected = date('m');
			$lastDateMonth = date($yearselected.'-'.$monthselected.'-t');
			$lastDayMonth = date('t',strtotime($lastDateMonth));
		}
		
		$workinghoursql = "SELECT attn.*,u.name as user_name,Organization.name as organization_name,
				GROUP_CONCAT(DISTINCT attn.cdate SEPARATOR '||') AS attn_time
				FROM attendances attn
				LEFT JOIN users u ON attn.user_id = u.id
				LEFT JOIN user_organizations Uorganization ON Uorganization.user_id = u.id			
				LEFT JOIN organizations Organization ON Uorganization.organization_id = Organization.id
				WHERE u.status = 1";
		
		if ($userRoles->hasRole(['Admin','Staff'])) {
			$userSelected 		= $userId;
			$departmentSelected = $deptId;
        }
		
		if($monthselected){
			$workinghoursql .= " AND MONTH(attn.cdate) = '".$monthselected."' ";
		}
		if($yearselected){
			$workinghoursql .= " AND YEAR(attn.cdate) = '".$yearselected."' ";
		}
		
		if ($departmentSelected && $userSelected!=$userId){
			//$workinghoursql .= " AND Uorganization.organization_id = '".$departmentSelected."'";
		} else if ($userSelected!=$userId) {
			//$workinghoursql .= " AND Uorganization.organization_id = '".$deptId."'";
			$departmentSelected = $deptId;			
		} else {
			$departmentSelected = $deptId;
		}
		if ($userSelected){
			$workinghoursql .= " AND u.id = '".$userSelected."'";
		} else {
			$workinghoursql .= " AND u.id = '".$userId."'";
			$userSelected = $userId;
		}
		$workinghoursql .= " GROUP BY DATE_FORMAT(attn.cdate, '%Y-%m-%d')  ORDER BY u.card_no";		
		
		$workinghourresults = $connection->execute($workinghoursql)->fetchAll('assoc');
		
		$this->viewBuilder()->options([
            'pdfConfig' => [
                'orientation' => 'landscape',
                'filename' => 'StaffWorkingHourReport.pdf'
            ]
        ]);

		$this->set('result',$workinghourresults);
        $this->set(compact('result','workinghoursql','sqldepartment','resultDepartment', 'userRoles','departments','users','filterSelected','departmentSelected','monthselected','lastDayMonth','userSelected','filterSelected','yearselected'));
		$this->set('_serialize', ['report']);
    }
	public function exportExcelworkinghour()
    {
		$departmentSelected = $this->request->query['department'];
		$userSelected = $this->request->query['user'];
		$monthselected = $this->request->query('att_month');
		$yearselected = $this->request->query('att_year');
		
		if ($monthselected){
			$lastDateMonth = date('Y-'.$monthselected.'-t');
			$lastDayMonth = date('t',strtotime($lastDateMonth));
		}
		
		$workinghoursql = "SELECT attn.*,u.name as user_name,Organization.name as organization_name,
				GROUP_CONCAT(DISTINCT attn.cdate SEPARATOR '||') AS attn_time
				FROM attendances attn
				LEFT JOIN users u ON attn.user_id = u.id
				LEFT JOIN user_organizations Uorganization ON Uorganization.user_id = u.id			
				LEFT JOIN organizations Organization ON Uorganization.organization_id = Organization.id
				WHERE u.status = 1";
		if($monthselected){
			$workinghoursql .= " AND MONTH(attn.cdate) = '".$monthselected."' ";
		}
		if($yearselected){
			$workinghoursql .= " AND YEAR(attn.cdate) = '".$yearselected."' ";
		}
		if ($userSelected){
			$workinghoursql .= " AND u.id = '".$userSelected."'";
		}
		$workinghoursql .= " GROUP BY DATE_FORMAT(attn.cdate, '%Y-%m-%d')  ORDER BY u.card_no";	
		
		$connection = ConnectionManager::get('default');
		$results = $connection->execute($workinghoursql)->fetchAll('assoc');
		
		
		//start to export
		if ($departmentSelected){
			$outputdepartment = $results[0]['organization_name'];
		}else{
			$outputdepartment = __('All');
		}
		if ($userSelected){
			$outputuser = $results[0]['user_name'];
		}else{
			$outputuser = __('All');
		}
		
		$file_date=date('dMY');
		$file_fullname = $file_date.'_WorkingHourReport';
		$now = \Cake\I18n\Time::now();
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="'.$file_fullname.'.csv"');
		$output= fopen('php://output', 'w');
		//output header
		fputcsv($output,array(__('Report Type'), __('Staff Working Hour')));
		
		fputcsv($output,array(__('Department'),$outputdepartment));
		fputcsv($output,array(__("Staff's Name"),$outputuser));
		fputcsv($output,array(__('Month'),$monthselected));
		fputcsv($output,array(''));
		
		//output column headings
		fputcsv($output, array(__('Bil'), __('Date'), __('In Time'), __('Out Time'), __('Total Hours')));
		$count_no=1;
		foreach ($results as $key => $user){			
			$results = explode("||",$user['attn_time']);
			
			$diff = strtotime($results[1]) - strtotime($results[0]);
			$hours = $diff / ( 60 * 60 );
			$latestDate = date('Y-m-d',strtotime($results[0]));
			
			if ($hours > 0){
				$thours = round($hours, 2). __('Hour');
			} else {
				$thours = '';
			}
			
			$arr_data[date('Y-m-d',strtotime($results[0]))]['in_time'] = date('H:i:s',strtotime($results[0]));
			$arr_data[date('Y-m-d',strtotime($results[0]))]['out_time'] = date('H:i:s',strtotime($results[1]));
			$arr_data[date('Y-m-d',strtotime($results[0]))]['total_hour'] = $thours;
		}
		for($daymonth=1;$daymonth<=$lastDayMonth;$daymonth++){
			if ($daymonth<10){
				$daymonth = '0'.$daymonth;
			}
			$currentDate = date('Y-'.$monthselected.'-'.$daymonth);
			$daynow = date('l', strtotime($currentDate));
			
			if(isset($arr_data[$currentDate])){ 
				$sub = ','.$arr_data[$currentDate]['in_time'].','.$arr_data[$currentDate]['out_time'].','.$arr_data[$currentDate]['total_hour'];
			} else{
				$sub = ',,,';
			}
			$data[] = $count_no.','.$currentDate.' ('.$daynow.')'.$sub;
			
			
			$count_no++;
		}
		
		$size=count($data);
		$count=0;
		
		while($count<$size){
			fputcsv($output, explode(',', $data[$count]));
			$count++;
		}
		
			
		
		fclose($output); die();
	}
   
}
 
