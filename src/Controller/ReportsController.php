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
		
		
		$sql = "SELECT Users.*, Attendances.cdate as att_date,Attendances.attn_time, Attendances.attn_remarks,UserCards.card_colour
			FROM users Users
			LEFT JOIN (
				SELECT Attendances.cdate,Attendances.status,Attendances.user_id, 
				GROUP_CONCAT(DISTINCT Attendances.cdate SEPARATOR '||') AS attn_time,
				GROUP_CONCAT(DISTINCT Attendances.remarks SEPARATOR ',') AS attn_remarks
				FROM attendances Attendances 
				WHERE year(Attendances.cdate) = '".$tempYear."'
				AND month(Attendances.cdate) = '".$tempMonth."'
				AND day(Attendances.cdate) = '".$tempDay."'
				GROUP BY Attendances.user_id
			)Attendances ON Users.id = Attendances.user_id
			LEFT JOIN (
				SELECT ucards.user_id,c.name as card_colour FROM user_cards ucards
				LEFT JOIN cards c ON ucards.card_id=c.id 
				WHERE date(ucards.cdate) = '".$dateselected."'
				GROUP BY ucards.user_id
			) UserCards ON UserCards.user_id = Users.id
			LEFT JOIN user_organizations Uorganization ON Uorganization.user_id = Users.id
			WHERE Users.status = 1";
		
		if ($departmentSelected){
			$sql .= " AND Uorganization.id = '".$departmentSelected."'";
		}
		if ($userSelected){
			$sql .= " AND Users.id = '".$userSelected."'";
		}
		$sql .= " ORDER BY Users.name";
		
		$connection = ConnectionManager::get('default');
		$results = $connection->execute($sql)->fetchAll('assoc');
		
		$this->viewBuilder()->options([
            'pdfConfig' => [
                'orientation' => 'landscape',
                'filename' => 'DailyReport.pdf'
            ]
        ]);

		$this->set('result',$results);
        $this->set(compact('result','dateselected', 'userRoles','departments','users','filterSelected','departmentSelected','userSelected'));
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
		$thisweekStart	= date('Y-m-d', strtotime( 'monday this week' ) );
		$thisweekEnd	= date( 'Y-m-d', strtotime( 'friday this week' ) );
		
		if ($dateselected){
			$thisweekStart	= date('Y-m-d', strtotime( 'monday this week',strtotime($dateselected)));
			$thisweekEnd	= date('Y-m-d', strtotime( 'friday this week',strtotime($dateselected)));
		}else {
			$dateselected = date( 'Y-m-d');
		}
		
		$weeklysql = "SELECT Users.*,Attendances.total_late,cardinfo.card_colour as card_colour 
				FROM users Users
				LEFT JOIN (
				SELECT count(Attendances.user_id) as total_late,Attendances.cdate,Attendances.user_id
				FROM attendances Attendances 
				WHERE  DATE_FORMAT(Attendances.cdate, '%Y-%m-%d')>='".$thisweekStart."'
				AND DATE_FORMAT(Attendances.cdate, '%Y-%m-%d')<='".$thisweekEnd."'
				AND DATE_FORMAT(Attendances.cdate, '%H:%i:%s')>='09:00:00'
				AND status = 1 
				GROUP BY Attendances.user_id
				)Attendances ON Users.id = Attendances.user_id
				LEFT JOIN (
					SELECT Ucards.cdate,Ucards.card_id,Ucards.user_id,Ucards.remarks,cards.name as card_colour
					from user_cards Ucards
					LEFT JOIN cards Cards ON Cards.id=Ucards.card_id 
					WHERE  DATE_FORMAT(Ucards.cdate, '%Y-%m-%d')>='".$thisweekStart."'
					AND DATE_FORMAT(Ucards.cdate, '%Y-%m-%d')<='".$thisweekEnd."'
					ORDER by cdate Desc
					LIMIT 18446744073709551615
				) cardinfo ON Users.id =cardinfo.user_id
				LEFT JOIN user_organizations Uorganization ON Uorganization.user_id = Users.id
				WHERE Users.status = 1";
		
		if ($departmentSelected){
			$weeklysql .= " AND Uorganization.id = '".$departmentSelected."'";
		}
		if ($userSelected){
			$weeklysql .= " AND Users.id = '".$userSelected."'";			
		}
		
		$weeklysql .= " GROUP BY  Users.id
				ORDER BY Users.name";
		
		$connection = ConnectionManager::get('default');
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
		
		$userId = $this->AuthUser->id();
        $user = $this->Users->find()->contain(['Roles'])->Where(['id' => "$userId"])->limit(1)->first();
        $userRoles = $this->Users->Roles->initRolesChecker($user->roles);
		$users = $this->Users->find('list');
        $departments = $this->Organizations->find('list');
		$thismonthStart	= date('Y-m-1');
		$thismonthEnd	= date( 'Y-m-t');
		
		if ($monthselected){
			$thismonthStart	=date("Y-".$monthselected."-1");
			$thismonthEnd	=date("Y-".$monthselected."-t");
		} else {
			$monthselected	= date('m');
		}
		
		$monthlysql = "SELECT Users.*,Attendances.total_late,cardinfo.card_colour as card_colour,
				cardinfo.remarks as card_remarks,cardinfo.cdate
				FROM users Users
				LEFT JOIN user_organizations UserOrganization ON UserOrganization.user_id = Users.id 
				LEFT JOIN (
				SELECT count(Attendances.user_id) as total_late,Attendances.cdate,Attendances.user_id
				FROM attendances Attendances 
				WHERE  DATE_FORMAT(Attendances.cdate, '%Y-%m-%d')>='".$thismonthStart."'
				AND DATE_FORMAT(Attendances.cdate, '%Y-%m-%d')<='".$thismonthEnd."'
				AND DATE_FORMAT(Attendances.cdate, '%H:%i:%s')>='09:00:00'
				AND status = 1 
				GROUP BY Attendances.user_id
				)Attendances ON Users.id = Attendances.user_id
				LEFT JOIN ( 
					SELECT Ucards.cdate,Ucards.card_id,Ucards.user_id,Ucards.remarks,cards.name as card_colour
					FROM user_cards Ucards
					LEFT JOIN cards Cards ON Cards.id=Ucards.card_id 
					WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."'
					ORDER by cdate ASC
					LIMIT 18446744073709551615 
				)cardinfo ON cardinfo.user_id=Users.id 
				WHERE Users.status = 1";
		
		if ($departmentSelected){
			$monthlysql .= " AND UserOrganization.id = '".$departmentSelected."'";
		}
		if ($userSelected){
			$monthlysql .= " AND Users.id = '".$userSelected."'";
		}
		$monthlysql .= " GROUP BY Users.id ORDER BY Users.name";
				
		$this->viewBuilder()->options([
            'pdfConfig' => [
                'orientation' => 'landscape',
                'filename' => 'MonthlyReport.pdf'
            ]
        ]);
		$connection = ConnectionManager::get('default');
		$monthlyresults = $connection->execute($monthlysql)->fetchAll('assoc');
		$this->set('monthlyresult',$monthlyresults);
        $this->set(compact('monthlyresult', 'monthlysql','monthselected','userRoles','departments','users','departmentSelected','userSelected'));
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
		
		//result for grade 55 and above
		$grade55sql = "SELECT count(ud.id) as total_officer,count(Ucards.green) as green,count(Ucards2.yellow) as yellow,
						count(Ucards3.red) as red
						FROM user_designations ud
						LEFT JOIN designations d ON d.id= ud.designation_id
						LEFT JOIN (
							SELECT card_id AS green,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND card_id =1
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards ON ud.user_id = Ucards.user_id
						LEFT JOIN (
							SELECT card_id as yellow,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND card_id =2
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC	
						)Ucards2 ON ud.user_id = Ucards2.user_id
						LEFT JOIN (
							SELECT card_id as red,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND card_id =3
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards3 ON ud.user_id = Ucards3.user_id
						WHERE d.gred >= 55
						ORDER BY ud.user_id";
		$grade55results = $connection->execute($grade55sql)->fetchAll('assoc');
		$this->set('grade55result',$grade55results);
		
		$totallate55sql = "SELECT count(Attendances.user_id) as total_late,Attendances.cdate,Attendances.user_id,
							Attendances.remarks
							FROM attendances Attendances 
							LEFT JOIN user_designations ud ON Attendances.user_id= ud.user_id
							LEFT JOIN designations d ON d.id= ud.designation_id
							WHERE MONTH(Attendances.cdate)= '".$monthselected."'
							AND DATE_FORMAT(Attendances.cdate, '%H:%i:%s')>='09:00:00'
							AND status = 1 
							AND d.gred >= 55 
							GROUP BY Attendances.user_id";
		$totallate55results = $connection->execute($totallate55sql)->fetchAll('assoc');
		$this->set('totallate55result',$totallate55results);
		
		//result for grade 48 to 54
		$grade4854sql = "SELECT count(ud.id) as total_officer,count(Ucards.green) as green,count(Ucards2.yellow) as yellow,
						count(Ucards3.red) as red
						FROM user_designations ud
						LEFT JOIN designations d ON d.id= ud.designation_id
						LEFT JOIN (
							SELECT card_id AS green,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND card_id =1
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards ON ud.user_id = Ucards.user_id
						LEFT JOIN (
							SELECT card_id as yellow,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND card_id =2
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC	
						)Ucards2 ON ud.user_id = Ucards2.user_id
						LEFT JOIN (
							SELECT card_id as red,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND card_id =3
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards3 ON ud.user_id = Ucards3.user_id
						WHERE d.gred >= 48 AND d.gred<=54
						ORDER BY ud.user_id";
		$grade4854results = $connection->execute($grade4854sql)->fetchAll('assoc');
		$this->set('grade4854result',$grade4854results);
		
		$totallate4854sql = "SELECT count(Attendances.user_id) as total_late,Attendances.cdate,Attendances.user_id,
							Attendances.remarks
							FROM attendances Attendances 
							LEFT JOIN user_designations ud ON Attendances.user_id= ud.user_id
							LEFT JOIN designations d ON d.id= ud.designation_id
							WHERE MONTH(Attendances.cdate)= '".$monthselected."'
							AND DATE_FORMAT(Attendances.cdate, '%H:%i:%s')>='09:00:00'
							AND status = 1 
							AND d.gred >= 48 AND d.gred<=54
							GROUP BY Attendances.user_id";
		$totallate4854results = $connection->execute($totallate4854sql)->fetchAll('assoc');
		$this->set('totallate4854result',$totallate4854results);
		
		//result for grade 41 to 44
		$grade4144sql = "SELECT count(ud.id) as total_officer,count(Ucards.green) as green,count(Ucards2.yellow) as yellow,
						count(Ucards3.red) as red
						FROM user_designations ud
						LEFT JOIN designations d ON d.id= ud.designation_id
						LEFT JOIN (
							SELECT card_id AS green,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND card_id =1
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards ON ud.user_id = Ucards.user_id
						LEFT JOIN (
							SELECT card_id as yellow,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND card_id =2
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC	
						)Ucards2 ON ud.user_id = Ucards2.user_id
						LEFT JOIN (
							SELECT card_id as red,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND card_id =3
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards3 ON ud.user_id = Ucards3.user_id
						WHERE d.gred >= 41 AND d.gred<=44
						ORDER BY ud.user_id";
		$grade4144results = $connection->execute($grade4144sql)->fetchAll('assoc');
		$this->set('grade4144result',$grade4144results);
		
		$totallate4144sql = "SELECT count(Attendances.user_id) as total_late,Attendances.cdate,Attendances.user_id,
							Attendances.remarks
							FROM attendances Attendances 
							LEFT JOIN user_designations ud ON Attendances.user_id= ud.user_id
							LEFT JOIN designations d ON d.id= ud.designation_id
							WHERE MONTH(Attendances.cdate)= '".$monthselected."'
							AND DATE_FORMAT(Attendances.cdate, '%H:%i:%s')>='09:00:00'
							AND status = 1 
							AND d.gred >= 41 AND d.gred<=44
							GROUP BY Attendances.user_id";
		$totallate4144results = $connection->execute($totallate4144sql)->fetchAll('assoc');
		$this->set('totallate4144result',$totallate4144results);
		
		//result for grade 17 to 40
		$grade1740sql = "SELECT count(ud.id) as total_officer,count(Ucards.green) as green,count(Ucards2.yellow) as yellow,
						count(Ucards3.red) as red
						FROM user_designations ud
						LEFT JOIN designations d ON d.id= ud.designation_id
						LEFT JOIN (
							SELECT card_id AS green,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND card_id =1
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards ON ud.user_id = Ucards.user_id
						LEFT JOIN (
							SELECT card_id as yellow,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND card_id =2
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC	
						)Ucards2 ON ud.user_id = Ucards2.user_id
						LEFT JOIN (
							SELECT card_id as red,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND card_id =3
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards3 ON ud.user_id = Ucards3.user_id
						WHERE d.gred >= 17 AND d.gred<=40
						ORDER BY ud.user_id";
		$grade1740results = $connection->execute($grade1740sql)->fetchAll('assoc');
		$this->set('grade1740result',$grade1740results);
		
		$totallate1740sql = "SELECT count(Attendances.user_id) as total_late,Attendances.cdate,Attendances.user_id,
							Attendances.remarks
							FROM attendances Attendances 
							LEFT JOIN user_designations ud ON Attendances.user_id= ud.user_id
							LEFT JOIN designations d ON d.id= ud.designation_id
							WHERE MONTH(Attendances.cdate)= '".$monthselected."'
							AND DATE_FORMAT(Attendances.cdate, '%H:%i:%s')>'09:00:00'
							AND status = 1 
							AND d.gred >= 17 AND d.gred<=40
							GROUP BY Attendances.user_id";
		$totallate1740results = $connection->execute($totallate1740sql)->fetchAll('assoc');
		$this->set('totallate1740result',$totallate1740results);
		
		//result for grade 1 to 16
		$grade116sql = "SELECT count(ud.id) as total_officer,count(Ucards.green) as green,count(Ucards2.yellow) as yellow,
						count(Ucards3.red) as red
						FROM user_designations ud
						LEFT JOIN designations d ON d.id= ud.designation_id
						LEFT JOIN (
							SELECT card_id AS green,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND card_id =1
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards ON ud.user_id = Ucards.user_id
						LEFT JOIN (
							SELECT card_id as yellow,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND card_id =2
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC	
						)Ucards2 ON ud.user_id = Ucards2.user_id
						LEFT JOIN (
							SELECT card_id as red,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND card_id =3
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards3 ON ud.user_id = Ucards3.user_id
						WHERE d.gred >= 1 AND d.gred<=16
						ORDER BY ud.user_id";
		$grade116results = $connection->execute($grade116sql)->fetchAll('assoc');
		$this->set('grade116result',$grade116results);
		
		$totallate116sql = "SELECT count(Attendances.user_id) as total_late,Attendances.cdate,Attendances.user_id, 			
							Attendances.remarks
							FROM attendances Attendances 
							LEFT JOIN user_designations ud ON Attendances.user_id= ud.user_id
							LEFT JOIN designations d ON d.id= ud.designation_id
							WHERE MONTH(Attendances.cdate)= '".$monthselected."'
							AND DATE_FORMAT(Attendances.cdate, '%H:%i:%s')>='09:00:00'
							AND status = 1 
							AND d.gred >= 1 AND d.gred<=16
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
        $this->set(compact('grade55result','grade4854result','grade4144result','grade1740result','grade116result', 'totallate55result','totallate4854result','totallate4144result','totallate1740result','totallate1740sql','totallate116result','monthselected','totallate4854sql','userRoles','departments','users','departmentSelected','userSelected'));
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
		
		$sql = "SELECT Users.*, Attendances.cdate as att_date,Attendances.attn_time, Attendances.attn_remarks,UserCards.card_colour
			FROM users Users
			LEFT JOIN (
				SELECT Attendances.cdate,Attendances.status,Attendances.user_id, 
				GROUP_CONCAT(DISTINCT Attendances.cdate SEPARATOR '||') AS attn_time,
				GROUP_CONCAT(DISTINCT Attendances.remarks SEPARATOR ',') AS attn_remarks
				FROM attendances Attendances 
				WHERE year(Attendances.cdate) = '".$tempYear."'
				AND month(Attendances.cdate) = '".$tempMonth."'
				AND day(Attendances.cdate) = '".$tempDay."'
				GROUP BY Attendances.user_id
			)Attendances ON Users.id = Attendances.user_id
			LEFT JOIN (
				SELECT ucards.user_id,c.name as card_colour FROM user_cards ucards
				LEFT JOIN cards c ON ucards.card_id=c.id 
				WHERE date(ucards.cdate) = '".$dateselected."'
				GROUP BY ucards.user_id
			) UserCards ON UserCards.user_id = Users.id
			LEFT JOIN user_organizations Uorganization ON Uorganization.user_id = Users.id
			WHERE Users.status = 1";
		
		if ($departmentSelected){
			$sql .= " AND Uorganization.id = '".$departmentSelected."'";
		}
		if ($userSelected){
			$sql .= " AND Users.id = '".$userSelected."'";
		}
		$sql .= " ORDER BY Users.name";
		
		$connection = ConnectionManager::get('default');
		$results = $connection->execute($sql)->fetchAll('assoc');
		
		
		//start to export
		if ($departmentSelected){
			$outputdepartment = $results[0]['organization_name'];
		}else{
			$outputdepartment = 'All';
		}
		if ($userSelected){
			$outputuser = $results[0]['name'];
		}else{
			$outputuser = 'All';
		}
		
		$file_date=date('dMY');
		$file_fullname = $file_date.'_DailyReport';
		$now = \Cake\I18n\Time::now();
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="'.$file_fullname.'.csv"');
		$output= fopen('php://output', 'w');
		//output header
		fputcsv($output,array('Report Type', 'Daily'));
		
		fputcsv($output,array('Date',$dateselected));
		fputcsv($output,array('Department',$outputdepartment));
		fputcsv($output,array('User',$outputuser));
		fputcsv($output,array(''));
		
		//output column headings
		fputcsv($output, array('Bil', 'Name', 'Card No', 'In Time', 'Out Time', 'Remarks', 'Total Hour'));
		$count_no=1;

		$totalyellow = 0;
		$totalred = 0;
		$totalgreen = 0;
		
		foreach ($results as $key => $user){
			$totalhours = '';
			$intime = '';
			$outtime = '';
			
			$result = explode("||",$user['attn_time']);
			$diff = strtotime($result[1]) - strtotime($result[0]);
			$hours = $diff / ( 60 * 60 );
			
			$showData = 1;
			if($filterSelected == 1){
				$showData = 0;
				if(date('H:i:s',strtotime($result[0]))	 > date('H:i:s',strtotime('09:00:00'))){
					$showData = 1;
				}
			}if($filterSelected == 2){
				$showData = 0;
				if($user['attn_time'] != '' && $hours < '9'){
					$showData = 1;
				}
			}if($filterSelected == 3){
				$showData = 0;
				if($user['attn_time'] == ''){
					$showData = 1;
				}
			}
			
			if ($showData == 1){
				if ($user['card_colour'] == 'Yellow'){ $totalyellow += 1;}
				if ($user['card_colour'] == 'Green'){ $totalgreen += 1;}
				if ($user['card_colour'] == 'Red'){ $totalred += 1;}
				if($hours>0){ $totalhours = round($hours, 2);}
				if ($result[0] !=''){ $intime = date('H:i:s',strtotime($result[0]));}
				if ($result[1] !=''){ $outtime = date('H:i:s',strtotime($result[1]));}
				
				$data[]=$count_no .','.$user['name'] .','.$user['card_no'].','.$intime.','.$outtime.','.$user['attn_remarks'].','.$totalhours;
				$count_no++;	
			}
		}	
		$total_officer = $count_no - 1;
		$data[]=',,,,,'.'Total Officer'.','.$total_officer;		
		$count_no++;
		$data[]=',,,,,'.'Total Officer That Hold Red Cards'.','.$totalred;	
		$count_no++;		
		$data[]=',,,,,'.'Total Officer That Hold Green Cards'.','.$totalgreen;		
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
		
		$thisweekStart	= date('Y-m-d', strtotime( 'monday this week' ) );
		$thisweekEnd	= date( 'Y-m-d', strtotime( 'friday this week' ) );
		
		if ($dateselected){
			$thisweekStart	= date('Y-m-d', strtotime( 'monday this week',strtotime($dateselected)));
			$thisweekEnd	= date('Y-m-d', strtotime( 'friday this week',strtotime($dateselected)));
		}else {
			$dateselected = date( 'Y-m-d');
		}
		
		$weeklysql = "SELECT Users.*,Attendances.total_late,cardinfo.card_colour as card_colour 
				FROM users Users
				LEFT JOIN (
				SELECT count(Attendances.user_id) as total_late,Attendances.cdate,Attendances.user_id
				FROM attendances Attendances 
				WHERE  DATE_FORMAT(Attendances.cdate, '%Y-%m-%d')>='".$thisweekStart."'
				AND DATE_FORMAT(Attendances.cdate, '%Y-%m-%d')<='".$thisweekEnd."'
				AND DATE_FORMAT(Attendances.cdate, '%H:%i:%s')>='09:00:00'
				AND status = 1 
				GROUP BY Attendances.user_id
				)Attendances ON Users.id = Attendances.user_id
				LEFT JOIN (
					SELECT Ucards.cdate,Ucards.card_id,Ucards.user_id,Ucards.remarks,cards.name as card_colour
					from user_cards Ucards
					LEFT JOIN cards Cards ON Cards.id=Ucards.card_id 
					WHERE  DATE_FORMAT(Ucards.cdate, '%Y-%m-%d')>='".$thisweekStart."'
					AND DATE_FORMAT(Ucards.cdate, '%Y-%m-%d')<='".$thisweekEnd."'
					ORDER by cdate Desc
					LIMIT 18446744073709551615
				) cardinfo ON Users.id =cardinfo.user_id
				LEFT JOIN user_organizations Uorganization ON Uorganization.user_id = Users.id
				WHERE Users.status = 1";
		
		if ($departmentSelected){
			$weeklysql .= " AND Uorganization.id = '".$departmentSelected."'";
		}
		if ($userSelected){
			$weeklysql .= " AND Users.id = '".$userSelected."'";			
		}
		
		$weeklysql .= " GROUP BY  Users.id
				ORDER BY Users.name";
		
		$connection = ConnectionManager::get('default');
		$weeklyresults = $connection->execute($weeklysql)->fetchAll('assoc');
		
		
		//start to export
		if ($departmentSelected){
			$outputdepartment = $weeklyresults[0]['organization_name'];
		}else{
			$outputdepartment = 'All';
		}
		if ($userSelected){
			$outputuser = $weeklyresults[0]['name'];
		}else{
			$outputuser = 'All';
		}
		
		$file_date=date('dMY');
		$file_fullname = $file_date.'_WeeklyReport';
		$now = \Cake\I18n\Time::now();
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="'.$file_fullname.'.csv"');
		$output= fopen('php://output', 'w');
		//output header
		fputcsv($output,array('Report Type', 'Weekly'));
		
		fputcsv($output,array('Date',$thisweekStart.' To '.$thisweekEnd));
		fputcsv($output,array('Department',$outputdepartment));
		fputcsv($output,array('User',$outputuser));
		fputcsv($output,array(''));
		
		//output column headings
		fputcsv($output, array('Bil', 'Name', 'Card No', 'red card in a week', 'red card in a week'));
		$count_no=1;
		
		$totalyellow = 0;
		$totalred = 0;
		$totalgreen = 0;
		foreach ($weeklyresults as $key => $user){
			if ($user['card_colour'] == 'Yellow'){ $totalyellow += 1;}
			if ($user['card_colour'] == 'Green'){ $totalgreen += 1;}
			if ($user['card_colour'] == 'Red'){ $totalred += 1;}
						
			$data[]=$count_no .','.$user['name'] .','.$user['card_no'].','.$user['total_late'].','.$user['card_colour'];
			$count_no++;	
		}
		$data[]=',,,,,';
		$total_officer = $count_no - 1;
		$data[]=',,,'.'Total Officer'.','.$total_officer;		
		$count_no++;
		$data[]=',,,'.'Total Officer That Hold Red Cards'.','.$totalred;	
		$count_no++;		
		$data[]=',,,'.'Total Officer That Hold Green Cards'.','.$totalgreen;		
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
		$att_month = $this->request->query['monthselected'];
		$departmentSelected = $this->request->query('department');
        $userSelected = $this->request->query('user');
		
		if ($monthselected){
			$thismonthStart	=date("Y-".$monthselected."-1");
			$thismonthEnd	=date("Y-".$monthselected."-t");
		} else {
			$monthselected	= date('m');
		}
		
		$monthlysql = "SELECT Users.*,Attendances.total_late,cardinfo.card_colour as card_colour,
				cardinfo.remarks as card_remarks,cardinfo.cdate
				FROM users Users
				LEFT JOIN user_organizations UserOrganization ON UserOrganization.user_id = Users.id 
				LEFT JOIN (
				SELECT count(Attendances.user_id) as total_late,Attendances.cdate,Attendances.user_id
				FROM attendances Attendances 
				WHERE  DATE_FORMAT(Attendances.cdate, '%Y-%m-%d')>='".$thismonthStart."'
				AND DATE_FORMAT(Attendances.cdate, '%Y-%m-%d')<='".$thismonthEnd."'
				AND DATE_FORMAT(Attendances.cdate, '%H:%i:%s')>='09:00:00'
				AND status = 1 
				GROUP BY Attendances.user_id
				)Attendances ON Users.id = Attendances.user_id
				LEFT JOIN ( 
					SELECT Ucards.cdate,Ucards.card_id,Ucards.user_id,Ucards.remarks,cards.name as card_colour
					FROM user_cards Ucards
					LEFT JOIN cards Cards ON Cards.id=Ucards.card_id 
					WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."'
					ORDER by cdate ASC
					LIMIT 18446744073709551615 
				)cardinfo ON cardinfo.user_id=Users.id 
				WHERE Users.status = 1";
		
		if ($departmentSelected){
			$monthlysql .= " AND UserOrganization.id = '".$departmentSelected."'";
		}
		if ($userSelected){
			$monthlysql .= " AND Users.id = '".$userSelected."'";
		}
		$monthlysql .= " GROUP BY Users.id ORDER BY Users.name";
				
		
		$connection = ConnectionManager::get('default');
		$monthlyresults = $connection->execute($monthlysql)->fetchAll('assoc');
		
		
		//start to export
		if ($departmentSelected){
			$outputdepartment = $monthlyresults[0]['organization_name'];
		}else{
			$outputdepartment = 'All';
		}
		if ($userSelected){
			$outputuser = $monthlyresults[0]['name'];
		}else{
			$outputuser = 'All';
		}
		
		
		
		$file_date=date('dMY');
		$file_fullname = $file_date.'_MonthlyReport';
		$now = \Cake\I18n\Time::now();
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="'.$file_fullname.'.csv"');
		$output= fopen('php://output', 'w');
		//output header
		fputcsv($output,array('Report Type', 'Monthly'));
		
		fputcsv($output,array('Month',$monthselected));
		fputcsv($output,array('Department',$outputdepartment));
		fputcsv($output,array('User',$outputuser));
		fputcsv($output,array(''));
		
		//output column headings
		fputcsv($output, array('Bil', 'Name', 'Grade', 'Card No', 'Total Late', 'Officer Approval', 'Card Colour', 'Remarks'));
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
			if ($user['total_late']>0){ $officerApproval = 'yes'; }else{ $officerApproval = '-'; }			
			
			$data[]=$count_no .','.$user['name'] .','.$user['grade'] .','.$user['card_no'].','.$totalLate.','.$officerApproval.','.$user['card_colour'].','.$user['card_remarks'];
			$count_no++;	
		}		
		$data[]=',,,,,,,,';
		$total_officer = $count_no - 1;
		$data[]=',,,,,,'.'Total Officer'.','.$total_officer;		
		$count_no++;
		$data[]=',,,,,,'.'Total Officer Late More Than 3 Times'.','.$total3times;	
		$count_no++;
		$data[]=',,,,,,'.'Total Officer That Hold Red Cards'.','.$totalred;	
		$count_no++;		
		$data[]=',,,,,,'.'Total Officer That Hold Green Cards'.','.$totalgreen;		
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
		
		if ($monthselected){
		} else {
			$monthselected	= date('m');
		}
		
		//result for grade 55 and above
		$grade55sql = "SELECT count(ud.id) as total_officer,count(Ucards.green) as green,count(Ucards2.yellow) as yellow,
						count(Ucards3.red) as red
						FROM user_designations ud
						LEFT JOIN designations d ON d.id= ud.designation_id
						LEFT JOIN (
							SELECT card_id AS green,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND card_id =1
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards ON ud.user_id = Ucards.user_id
						LEFT JOIN (
							SELECT card_id as yellow,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND card_id =2
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC	
						)Ucards2 ON ud.user_id = Ucards2.user_id
						LEFT JOIN (
							SELECT card_id as red,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND card_id =3
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards3 ON ud.user_id = Ucards3.user_id
						WHERE d.gred >= 55
						ORDER BY ud.user_id";
		$grade55results = $connection->execute($grade55sql)->fetchAll('assoc');
		
		$totallate55sql = "SELECT count(Attendances.user_id) as total_late,Attendances.cdate,Attendances.user_id,
							Attendances.remarks
							FROM attendances Attendances 
							LEFT JOIN user_designations ud ON Attendances.user_id= ud.user_id
							LEFT JOIN designations d ON d.id= ud.designation_id
							WHERE MONTH(Attendances.cdate)= '".$monthselected."'
							AND DATE_FORMAT(Attendances.cdate, '%H:%i:%s')>='09:00:00'
							AND status = 1 
							AND d.gred >= 55 
							GROUP BY Attendances.user_id";
		$totallate55results = $connection->execute($totallate55sql)->fetchAll('assoc');
		
		//result for grade 48 to 54
		$grade4854sql = "SELECT count(ud.id) as total_officer,count(Ucards.green) as green,count(Ucards2.yellow) as yellow,
						count(Ucards3.red) as red
						FROM user_designations ud
						LEFT JOIN designations d ON d.id= ud.designation_id
						LEFT JOIN (
							SELECT card_id AS green,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND card_id =1
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards ON ud.user_id = Ucards.user_id
						LEFT JOIN (
							SELECT card_id as yellow,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND card_id =2
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC	
						)Ucards2 ON ud.user_id = Ucards2.user_id
						LEFT JOIN (
							SELECT card_id as red,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND card_id =3
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards3 ON ud.user_id = Ucards3.user_id
						WHERE d.gred >= 48 AND d.gred<=54
						ORDER BY ud.user_id";
		$grade4854results = $connection->execute($grade4854sql)->fetchAll('assoc');

		$totallate4854sql = "SELECT count(Attendances.user_id) as total_late,Attendances.cdate,Attendances.user_id,
							Attendances.remarks
							FROM attendances Attendances 
							LEFT JOIN user_designations ud ON Attendances.user_id= ud.user_id
							LEFT JOIN designations d ON d.id= ud.designation_id
							WHERE MONTH(Attendances.cdate)= '".$monthselected."'
							AND DATE_FORMAT(Attendances.cdate, '%H:%i:%s')>='09:00:00'
							AND status = 1 
							AND d.gred >= 48 AND d.gred<=54
							GROUP BY Attendances.user_id";
		$totallate4854results = $connection->execute($totallate4854sql)->fetchAll('assoc');
		
		//result for grade 41 to 44
		$grade4144sql = "SELECT count(ud.id) as total_officer,count(Ucards.green) as green,count(Ucards2.yellow) as yellow,
						count(Ucards3.red) as red
						FROM user_designations ud
						LEFT JOIN designations d ON d.id= ud.designation_id
						LEFT JOIN (
							SELECT card_id AS green,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND card_id =1
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards ON ud.user_id = Ucards.user_id
						LEFT JOIN (
							SELECT card_id as yellow,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND card_id =2
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC	
						)Ucards2 ON ud.user_id = Ucards2.user_id
						LEFT JOIN (
							SELECT card_id as red,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND card_id =3
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards3 ON ud.user_id = Ucards3.user_id
						WHERE d.gred >= 41 AND d.gred<=44
						ORDER BY ud.user_id";
		$grade4144results = $connection->execute($grade4144sql)->fetchAll('assoc');
		
		$totallate4144sql = "SELECT count(Attendances.user_id) as total_late,Attendances.cdate,Attendances.user_id,
							Attendances.remarks
							FROM attendances Attendances 
							LEFT JOIN user_designations ud ON Attendances.user_id= ud.user_id
							LEFT JOIN designations d ON d.id= ud.designation_id
							WHERE MONTH(Attendances.cdate)= '".$monthselected."'
							AND DATE_FORMAT(Attendances.cdate, '%H:%i:%s')>='09:00:00'
							AND status = 1 
							AND d.gred >= 41 AND d.gred<=44
							GROUP BY Attendances.user_id";
		$totallate4144results = $connection->execute($totallate4144sql)->fetchAll('assoc');
		
		//result for grade 17 to 40
		$grade1740sql = "SELECT count(ud.id) as total_officer,count(Ucards.green) as green,count(Ucards2.yellow) as yellow,
						count(Ucards3.red) as red
						FROM user_designations ud
						LEFT JOIN designations d ON d.id= ud.designation_id
						LEFT JOIN (
							SELECT card_id AS green,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND card_id =1
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards ON ud.user_id = Ucards.user_id
						LEFT JOIN (
							SELECT card_id as yellow,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND card_id =2
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC	
						)Ucards2 ON ud.user_id = Ucards2.user_id
						LEFT JOIN (
							SELECT card_id as red,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND card_id =3
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards3 ON ud.user_id = Ucards3.user_id
						WHERE d.gred >= 17 AND d.gred<=40
						ORDER BY ud.user_id";
		$grade1740results = $connection->execute($grade1740sql)->fetchAll('assoc');
		
		$totallate1740sql = "SELECT count(Attendances.user_id) as total_late,Attendances.cdate,Attendances.user_id,
							Attendances.remarks
							FROM attendances Attendances 
							LEFT JOIN user_designations ud ON Attendances.user_id= ud.user_id
							LEFT JOIN designations d ON d.id= ud.designation_id
							WHERE MONTH(Attendances.cdate)= '".$monthselected."'
							AND DATE_FORMAT(Attendances.cdate, '%H:%i:%s')>'09:00:00'
							AND status = 1 
							AND d.gred >= 17 AND d.gred<=40
							GROUP BY Attendances.user_id";
		$totallate1740results = $connection->execute($totallate1740sql)->fetchAll('assoc');

		
		//result for grade 1 to 16
		$grade116sql = "SELECT count(ud.id) as total_officer,count(Ucards.green) as green,count(Ucards2.yellow) as yellow,
						count(Ucards3.red) as red
						FROM user_designations ud
						LEFT JOIN designations d ON d.id= ud.designation_id
						LEFT JOIN (
							SELECT card_id AS green,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND card_id =1
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards ON ud.user_id = Ucards.user_id
						LEFT JOIN (
							SELECT card_id as yellow,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND card_id =2
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC	
						)Ucards2 ON ud.user_id = Ucards2.user_id
						LEFT JOIN (
							SELECT card_id as red,Ucards.user_id
							FROM user_cards Ucards 
							WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."' AND card_id =3
							GROUP BY Ucards.user_id
							ORDER BY Ucards.cdate DESC
						)Ucards3 ON ud.user_id = Ucards3.user_id
						WHERE d.gred >= 1 AND d.gred<=16
						ORDER BY ud.user_id";
		$grade116results = $connection->execute($grade116sql)->fetchAll('assoc');

		
		$totallate116sql = "SELECT count(Attendances.user_id) as total_late,Attendances.cdate,Attendances.user_id, 			
							Attendances.remarks
							FROM attendances Attendances 
							LEFT JOIN user_designations ud ON Attendances.user_id= ud.user_id
							LEFT JOIN designations d ON d.id= ud.designation_id
							WHERE MONTH(Attendances.cdate)= '".$monthselected."'
							AND DATE_FORMAT(Attendances.cdate, '%H:%i:%s')>='09:00:00'
							AND status = 1 
							AND d.gred >= 1 AND d.gred<=16
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
		fputcsv($output,array('Report Type', 'Monthly Summary'));
		
		fputcsv($output,array('Month',$monthselected));
		fputcsv($output,array(''));
		
		
		//output column headings
		fputcsv($output, array('Bil', 'Officer Group', 'Total Officer', '','Card Colour','' ,'Three late in a month (total officer)', 'Remarks'));
		fputcsv($output, array('', '', '', 'Yellow', 'Green',  'Red',''));
		
		$count_no=1;

		
		$data[]=$count_no .','.'Higher Management Group'.','.$grade55results[0]['total_officer'] .','.$totalStaffYellow55 .','.$totalStaffGreen55.','.$totalStaffRed55.','.$totalLateOfficer55.','.$count55late[0]['remarks'];
		$count_no++;
		$data[]=$count_no .','.'Professional Management Group (Grade 48-54)'.','.$grade4854results[0]['total_officer'] .','.$totalStaffYellow4854 .','.$totalStaffGreen4854.','.$totalStaffRed4854.','.$totalLateOfficer4854.','.$count4854late[0]['remarks'];
		$count_no++;
		$data[]=$count_no .','.'Professional Management Group (Grade 41-44)'.','.$grade4144results[0]['total_officer'] .','.$totalStaffYellow4144 .','.$totalStaffGreen4144.','.$totalStaffRed4144.','.$totalLateOfficer4144.','.$count4144late[0]['remarks'];
		$count_no++;
		$data[]=$count_no .','.'Executing Group (Grade 17-40)'.','.$grade1740results[0]['total_officer'] .','.$totalStaffYellow1740 .','.$totalStaffGreen1740.','.$totalStaffRed1740.','.$totalLateOfficer1740.','.$count1740late[0]['remarks'];
		$count_no++;
		$data[]=$count_no .','.'Executing Group (Grade 1-16)'.','.$grade116results[0]['total_officer'] .','.$totalStaffYellow116 .','.$totalStaffGreen116.','.$totalStaffRed116.','.$totalLateOfficer116.','.$count116late[0]['remarks'];
		$count_no++;	
			
		//grand total count
		$gtotalstaff= $grade55results[0]['total_officer'] + $grade4854results[0]['total_officer'] + $grade4144results[0]['total_officer'] + $grade1740results[0]['total_officer'] + $grade116results[0]['total_officer'];
									
		$gtotalyellow= $totalStaffYellow55 + $totalStaffYellow4854 +$totalStaffYellow4144 + $totalStaffYellow1740 +$totalStaffYellow116;
		
		$gtotalgreen= $totalStaffGreen55 + $totalStaffGreen4854 +$totalStaffGreen4144 + $totalStaffGreen1740 + $totalStaffGreen116;
		
		$gtotalred= $totalStaffRed55 + $totalStaffRed4854 +$totalStaffRed4144 + $totalStaffRed1740 +$totalStaffRed116;
		
		$gtotal3times = $totalLateOfficer55 + $totalLateOfficer4854 + $totalLateOfficer4144 + $totalLateOfficer1740 + $totalLateOfficer116;
			
		$data[]=',,,,,,,';
		$data[]=','.'Grand Total'.','.$gtotalstaff.','.$gtotalyellow.','.$gtotalgreen.','.$gtotalred.','.$gtotal3times;
		$size=count($data);
		$count=0;
		
		while($count<$size){
			fputcsv($output, explode(',', $data[$count]));
			$count++;
		}
		
			
		
		fclose($output); die();
    }
	
	public function pdfDaily1()
    {
		$dateselected = $this->request->query['date_attendance'];
		$departmentSelected = $this->request->query['department'];
		$userSelected = $this->request->query['user'];
		
		
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
		
		$sql = "SELECT Users.*, Attendances.cdate as att_date,Attendances.attn_time, Attendances.attn_remarks, Orgatype.name as organization_name
			FROM users Users
			LEFT JOIN (
			SELECT Attendances.cdate,Attendances.status,Attendances.user_id, 
			GROUP_CONCAT(DISTINCT Attendances.cdate SEPARATOR '||') AS attn_time,
			GROUP_CONCAT(DISTINCT Attendances.remarks SEPARATOR ',') AS attn_remarks
			FROM attendances Attendances 
			WHERE year(Attendances.cdate) = '".$tempYear."'
			AND month(Attendances.cdate) = '".$tempMonth."'
			AND day(Attendances.cdate) = '".$tempDay."'
			GROUP BY Attendances.user_id
			)Attendances ON Users.id = Attendances.user_id
			LEFT JOIN user_organizations Uorganization ON Uorganization.user_id = Users.id			
			LEFT JOIN organizations Orgatype ON Uorganization.organization_id=Orgatype.id
			WHERE Users.status = 1";
		
		if ($departmentSelected){
			$sql .= " AND Uorganization.id = '".$departmentSelected."'";
		}
		if ($userSelected){
			$sql .= " AND Users.id = '".$userSelected."'";
		}
		$sql .= " ORDER BY Users.name";
		
		$connection = ConnectionManager::get('default');
		$results = $connection->execute($sql)->fetchAll('assoc');
		
		
		//start to export
		if ($departmentSelected){
			$outputdepartment = $results[0]['organization_name'];
		}else{
			$outputdepartment = 'All';
		}
		if ($userSelected){
			$outputuser = $results[0]['name'];
		}else{
			$outputuser = 'All';
		}
		
		$file_date=date('dMY');
		$file_fullname = $file_date.'_DailyReport';
		$now = \Cake\I18n\Time::now();
		$afile_path = "../webroot/files";
		if(is_dir($afile_path)==false) {
			mkdir($afile_path, 0777);
		}


		$filepath = $afile_path.'/'.$file_fullname.'.pdf';

		//save file to the server
		file_put_contents($filepath, $attachment);
		header('Content-Type: application/pdf');
		header("Content-Transfer-Encoding: Binary");
		header("Content-disposition: attachment; filename= ".$filename.".pdf");
		readfile($filepath);

		$output= fopen('php://output', 'w');
		//output header
		fputs($output,array('Report Type', 'Daily'));
		
		fputs($output,array('Date',$dateselected));
		fputs($output,array('Department',$outputdepartment));
		fputs($output,array('User',$outputuser));
		fputs($output,array(''));
		
		//output column headings
		fputs($output, array('Bil', 'Name', 'Card No', 'In Time', 'Out Time', 'Remarks'));
		$count_no=1;

		foreach ($results as $key => $user){
			$result = explode("||",$user['attn_time']);
			
			$data[]=$count_no .','.$user['name'] .','.$user['card_no'].','.$result[0].','.$result[1].','.$user['attn_remarks'];
			$count_no++;	
		}		
		$size=count($data);
		$count=0;
		
		while($count<$size){
			fputs($output, explode(',', $data[$count]));
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
		
   
}
 
