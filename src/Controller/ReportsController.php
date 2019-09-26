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
		

		$sqldaily = "SELECT Users.*, Attendances.cdate as att_date,Attendances.attn_time, Attendances.attn_remarks
			FROM users Users
			LEFT JOIN (
			SELECT Attendances.cdate,Attendances.status,Attendances.user_id, 
			GROUP_CONCAT(DISTINCT Attendances.cdate SEPARATOR '||') AS attn_time,
			GROUP_CONCAT(DISTINCT Attendances.remarks SEPARATOR ',') AS attn_remarks
			FROM attendances Attendances 
			WHERE year(`Attendances`.`cdate`) = '". $tempYear ."'
			AND month(Attendances.cdate) = '". $tempMonth ."'
			AND day(Attendances.cdate) = '". $tempDay ."'
			GROUP BY Attendances.user_id
			)Attendances ON Users.id = Attendances.user_id
			LEFT JOIN user_organizations Uorganization ON Uorganization.user_id = Users.id
			WHERE Users.status = 1";
		
		if ($departmentSelected){
			$sqldaily .= " AND Uorganization.id = '".$departmentSelected."'";
		}
		if ($userSelected){
			$sqldaily .= " AND Users.id = '".$userSelected."'";
		}
		$sqldaily .= " ORDER BY Users.name";
		
		$connection = ConnectionManager::get('default');
		$results = $connection->execute($sqldaily)->fetchAll('assoc');
		$this->set('result',$results);
        $this->set(compact('result','dateselected', 'userRoles','departments','users','departmentSelected','userSelected'));
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
				
		//take note organization id tak filter lg, hardcord only
		
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
		$thismonthStart	= date('Y-m-1');
		$thismonthEnd	= date( 'Y-m-t');
		
		if ($monthselected){
			$thismonthStart	=date("Y-".$monthselected."-1");
			$thismonthEnd	=date("Y-".$monthselected."-t");
		} else {
			$monthselected	= date('m');
		}
		
		//result for grade 55 and above
		$grade55sql = "SELECT count(ud.id) as total_officer,Ucards.green,Ucards.yellow,Ucards.red
						FROM user_designations ud
						LEFT JOIN designations d ON d.id= ud.designation_id
						LEFT JOIN (
						SELECT SUM(DISTINCT(Ucards.user_id) AND Ucards.card_id = 1) AS green,
						SUM(DISTINCT(Ucards.user_id) AND Ucards.card_id=2) AS yellow,
						SUM(DISTINCT(Ucards.user_id) AND Ucards.card_id=3) AS red,Ucards.user_id
						FROM user_cards Ucards 
						LEFT JOIN user_designations ud ON Ucards.user_id= ud.user_id
						LEFT JOIN designations d ON d.id= ud.designation_id
						WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."'
						AND d.gred >= 55
						ORDER BY Ucards.user_id
						)Ucards ON ud.user_id = Ucards.user_id
						WHERE d.gred >= 55
						ORDER BY ud.user_id";
		$grade55results = $connection->execute($grade55sql)->fetchAll('assoc');
		$this->set('grade55result',$grade55results);
		
		$totallate55sql = "SELECT count(Attendances.user_id) as total_late,Attendances.cdate,Attendances.user_id
							FROM attendances Attendances 
							LEFT JOIN user_designations ud ON Attendances.user_id= ud.user_id
							LEFT JOIN designations d ON d.id= ud.designation_id
							WHERE  DATE_FORMAT(Attendances.cdate, '%Y-%m-%d')>='".$thismonthStart."'
							AND DATE_FORMAT(Attendances.cdate, '%Y-%m-%d')<='".$thismonthEnd."'
							AND DATE_FORMAT(Attendances.cdate, '%H:%i:%s')>='09:00:00'
							AND status = 1 
							AND d.gred >= 55 
							GROUP BY Attendances.user_id";
		$totalalte55results = $connection->execute($totallate55sql)->fetchAll('assoc');
		$this->set('totallate55result',$totalalte55results);
		
		//result for grade 48 to 54
		$grade4854sql = "SELECT count(ud.id) as total_officer,Ucards.green,Ucards.yellow,Ucards.red
						FROM user_designations ud
						LEFT JOIN designations d ON d.id= ud.designation_id
						LEFT JOIN (
						SELECT SUM(DISTINCT(Ucards.user_id) AND Ucards.card_id = 1) AS green,
						SUM(DISTINCT(Ucards.user_id) AND Ucards.card_id=2) AS yellow,
						SUM(DISTINCT(Ucards.user_id) AND Ucards.card_id=3) AS red,Ucards.user_id
						FROM user_cards Ucards 
						LEFT JOIN user_designations ud ON Ucards.user_id= ud.user_id
						LEFT JOIN designations d ON d.id= ud.designation_id
						WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."'
						AND d.gred >= 48 AND d.gred<=54
						ORDER BY Ucards.user_id
						)Ucards ON ud.user_id = Ucards.user_id
						WHERE d.gred >= 48 AND d.gred<=54
						ORDER BY ud.user_id";
		$grade4854results = $connection->execute($grade4854sql)->fetchAll('assoc');
		$this->set('grade4854result',$grade4854results);
		
		$totallate4854sql = "SELECT count(Attendances.user_id) as total_late,Attendances.cdate,Attendances.user_id
							FROM attendances Attendances 
							LEFT JOIN user_designations ud ON Attendances.user_id= ud.user_id
							LEFT JOIN designations d ON d.id= ud.designation_id
							WHERE  DATE_FORMAT(Attendances.cdate, '%Y-%m-%d')>='".$thismonthStart."'
							AND DATE_FORMAT(Attendances.cdate, '%Y-%m-%d')<='".$thismonthEnd."'
							AND DATE_FORMAT(Attendances.cdate, '%H:%i:%s')>='09:00:00'
							AND status = 1 
							AND d.gred >= 48 AND d.gred<=54
							GROUP BY Attendances.user_id";
		$totallate4854results = $connection->execute($totallate4854sql)->fetchAll('assoc');
		$this->set('totallate4854result',$totallate4854results);
		
		//result for grade 41 to 44
		$grade4144sql = "SELECT count(ud.id) as total_officer,Ucards.green,Ucards.yellow,Ucards.red
						FROM user_designations ud
						LEFT JOIN designations d ON d.id= ud.designation_id
						LEFT JOIN (
						SELECT SUM(DISTINCT(Ucards.user_id) AND Ucards.card_id = 1) AS green,
						SUM(DISTINCT(Ucards.user_id) AND Ucards.card_id=2) AS yellow,
						SUM(DISTINCT(Ucards.user_id) AND Ucards.card_id=3) AS red,Ucards.user_id
						FROM user_cards Ucards 
						LEFT JOIN user_designations ud ON Ucards.user_id= ud.user_id
						LEFT JOIN designations d ON d.id= ud.designation_id
						WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."'
						AND d.gred >= 41 AND d.gred<=44
						ORDER BY Ucards.user_id
						)Ucards ON ud.user_id = Ucards.user_id
						WHERE d.gred >= 41 AND d.gred<=44
						ORDER BY ud.user_id";
		$grade4144results = $connection->execute($grade4144sql)->fetchAll('assoc');
		$this->set('grade4144result',$grade4144results);
		
		$totallate4144sql = "SELECT count(Attendances.user_id) as total_late,Attendances.cdate,Attendances.user_id
							FROM attendances Attendances 
							LEFT JOIN user_designations ud ON Attendances.user_id= ud.user_id
							LEFT JOIN designations d ON d.id= ud.designation_id
							WHERE  DATE_FORMAT(Attendances.cdate, '%Y-%m-%d')>='".$thismonthStart."'
							AND DATE_FORMAT(Attendances.cdate, '%Y-%m-%d')<='".$thismonthEnd."'
							AND DATE_FORMAT(Attendances.cdate, '%H:%i:%s')>='09:00:00'
							AND status = 1 
							AND d.gred >= 41 AND d.gred<=44
							GROUP BY Attendances.user_id";
		$totalalte4144results = $connection->execute($totallate4144sql)->fetchAll('assoc');
		$this->set('totallate4144result',$totalalte4144results);
		
		//result for grade 17 to 40
		$grade1740sql = "SELECT count(ud.id) as total_officer,Ucards.green,Ucards.yellow,Ucards.red
						FROM user_designations ud
						LEFT JOIN designations d ON d.id= ud.designation_id
						LEFT JOIN (
						SELECT SUM(DISTINCT(Ucards.user_id) AND Ucards.card_id = 1) AS green,
						SUM(DISTINCT(Ucards.user_id) AND Ucards.card_id=2) AS yellow,
						SUM(DISTINCT(Ucards.user_id) AND Ucards.card_id=3) AS red,Ucards.user_id
						FROM user_cards Ucards 
						LEFT JOIN user_designations ud ON Ucards.user_id= ud.user_id
						LEFT JOIN designations d ON d.id= ud.designation_id
						WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."'
						AND d.gred >= 17 AND d.gred<=40
						ORDER BY Ucards.user_id
						)Ucards ON ud.user_id = Ucards.user_id
						WHERE d.gred >= 17 AND d.gred<=40
						ORDER BY ud.user_id";
		$grade1740results = $connection->execute($grade1740sql)->fetchAll('assoc');
		$this->set('grade1740result',$grade1740results);
		
		$totallate1740sql = "SELECT count(Attendances.user_id) as total_late,Attendances.cdate,Attendances.user_id
							FROM attendances Attendances 
							LEFT JOIN user_designations ud ON Attendances.user_id= ud.user_id
							LEFT JOIN designations d ON d.id= ud.designation_id
							WHERE  DATE_FORMAT(Attendances.cdate, '%Y-%m-%d')>='".$thismonthStart."'
							AND DATE_FORMAT(Attendances.cdate, '%Y-%m-%d')<='".$thismonthEnd."'
							AND DATE_FORMAT(Attendances.cdate, '%H:%i:%s')>='09:00:00'
							AND status = 1 
							AND d.gred >= 17 AND d.gred<=40
							GROUP BY Attendances.user_id";
		$totalalte1740results = $connection->execute($totallate1740sql)->fetchAll('assoc');
		$this->set('totallate1740result',$totalalte1740results);
		
		//result for grade 1 to 16
		$grade116sql = "SELECT count(ud.id) as total_officer,Ucards.green,Ucards.yellow,Ucards.red
						FROM user_designations ud
						LEFT JOIN designations d ON d.id= ud.designation_id
						LEFT JOIN (
						SELECT SUM(DISTINCT(Ucards.user_id) AND Ucards.card_id = 1) AS green,
						SUM(DISTINCT(Ucards.user_id) AND Ucards.card_id=2) AS yellow,
						SUM(DISTINCT(Ucards.user_id) AND Ucards.card_id=3) AS red,Ucards.user_id
						FROM user_cards Ucards 
						LEFT JOIN user_designations ud ON Ucards.user_id= ud.user_id
						LEFT JOIN designations d ON d.id= ud.designation_id
						WHERE DATE_FORMAT(Ucards.cdate, '%m')='".$monthselected."'
						AND d.gred >= 1 AND d.gred<=16
						ORDER BY Ucards.user_id
						)Ucards ON ud.user_id = Ucards.user_id
						WHERE d.gred >= 1 AND d.gred<=16
						ORDER BY ud.user_id";
		$grade116results = $connection->execute($grade116sql)->fetchAll('assoc');
		$this->set('grade116result',$grade116results);
		
		$totallate116sql = "SELECT count(Attendances.user_id) as total_late,Attendances.cdate,Attendances.user_id
							FROM attendances Attendances 
							LEFT JOIN user_designations ud ON Attendances.user_id= ud.user_id
							LEFT JOIN designations d ON d.id= ud.designation_id
							WHERE  DATE_FORMAT(Attendances.cdate, '%Y-%m-%d')>='".$thismonthStart."'
							AND DATE_FORMAT(Attendances.cdate, '%Y-%m-%d')<='".$thismonthEnd."'
							AND DATE_FORMAT(Attendances.cdate, '%H:%i:%s')>='09:00:00'
							AND status = 1 
							AND d.gred >= 1 AND d.gred<=16
							GROUP BY Attendances.user_id";
		$totalalte116results = $connection->execute($totallate116sql)->fetchAll('assoc');
		$this->set('totallate116result',$totalalte116results);
		
		//connection to template
        $this->set(compact('grade55results','grade4854results','grade4144results','grade1740results','grade116results', 'totallate55result','totallate4854result','totallate4144result','totallate1740result','totallate116result','monthselected','totallate4854sql','userRoles','departments','users','departmentSelected','userSelected'));
        $this->set('_serialize', ['report']);
    }
    
	/**/
	
	/*SELECT count(Attendances.user_id) as total_late,Attendances.cdate,Attendances.user_id
	FROM attendances Attendances 
	LEFT JOIN user_designations ud ON Attendances.user_id= ud.user_id
	LEFT JOIN designations d ON d.id= ud.designation_id
	WHERE  DATE_FORMAT(Attendances.cdate, '%Y-%m-%d')>='2019-09-01"'
	AND DATE_FORMAT(Attendances.cdate, '%Y-%m-%d')<='2019-09-30'
	AND DATE_FORMAT(Attendances.cdate, '%H:%i:%s')>='09:00:00'
	AND status = 1 
	AND d.gred >= 17 AND d.gred<=40
	GROUP BY Attendances.user_id*/
   
}
