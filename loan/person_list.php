<?php 
require_once '../user/class.user.php';
session_start();
$user_login = new USER();

if(!$user_login->is_logged_in())
{
	$user_login->redirect('../user/index.php');
}
$sqlSelectSetting = ("SELECT directornumber,addressnumber,identitynumber,securitynumber,contractnumber,guarantornumber,identitydirectornumber from setting");
$stmtSetting = $user_login->runQuery($sqlSelectSetting);
$stmtSetting->execute(array());
$rowSetting = $stmtSetting->fetch();

$directorNumber = 	$rowSetting['directornumber'];
$addressNumber =  	$rowSetting['addressnumber'];
$identityNumber = 	$rowSetting['identitynumber'];
$identityDirectorNumber = 	$rowSetting['identitydirectornumber'];
$securityNumber = 	$rowSetting['securitynumber'];
$contractNumber = 	$rowSetting['contractnumber'];
$guarantorNumber = 	$rowSetting['guarantornumber'];

	$sqlSelectLoanApplications = ("SELECT personid,
											partyid as applicantnumber,
											title,
											firstname,
											lastname,
											othername,
											dateofbirth,
											nationality,
											religion,
											tribe,
											gender,
											maritalstatus,
											photopath,
											createddate
											FROM person");
	$stmtApplicant = $user_login->runQuery($sqlSelectLoanApplications);
	$stmtApplicant->execute(array());

//Define page variable	
$location = "Person List";
$title = "Person List";
$breadcumb ="Person List";
$breadcumbDescription=" View Person details, add identity, physical address and request loan";
$currentSymbo = "TZS";

include('../inc/header.php');?>
                                    <!-- BEGIN PAGE CONTENT INNER -->
                                    <div class="page-content-inner">
                                          <div class="row">
                                            <div class="col-md-12">
                                                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                                                <div class="portlet light ">
                                                    <div class="portlet-title">
                                                        <div class="caption font-dark">
                                                            <i class="icon-settings font-dark"></i>
                                                            <span class="caption-subject bold uppercase"><?php echo $title?></span>
                                                        </div>
                                                        <div class="tools"> </div>
                                                    </div>
                                                    <div class="portlet-body">
                                                        <table class="table table-striped table-bordered table-hover" id="sample_1">
                                                            <thead>
                                                                <tr>
																	<th>#</th>
																	<th>Full Name</th>
																	<th>Birth Date</th>
																	<th>Nationality</th>
																	<th>Marital Status</th>																	
																	<th>Created Date</th>
																	<th>Address</th>																	
																	<th>Identity</th>
																	<th>Details</th>
																	<th>Request Loan</th>                                                                  
                                                                </tr>
                                                            </thead>
                                                            <tfoot>
                                                                <tr>
                                                                    <th>#</th>
																	<th>Full Name</th>
																	<th>Birth Date</th>
																	<th>Nationality</th>																	
																	<th>Marital Status</th>																	
																	<th>Created Date</th>
																	<th>Address</th>																	
																	<th>Identity</th>
																	<th>Details</th>
																	<th>Request Loan</th> 
                                                                </tr>
                                                            </tfoot>
                                                            <tbody>
															<?php
	
	
	foreach ( $stmtApplicant->fetchAll () as $row ) {
		
		$sqlSelectIdentityCount = ("SELECT count(identityid) as identitycount FROM identity where partyid = ? ");
		$stmtLoanIdentityCount = $user_login->runQuery($sqlSelectIdentityCount);
		$stmtLoanIdentityCount->execute(array($row['applicantnumber']));
		$row2 = $stmtLoanIdentityCount->fetch();
		$identytyCount = $row2['identitycount'];
		
		$sqlSelectIdentityCountDirector = ("SELECT count(identityid) as identitycountd FROM identity,partyrole,organization where roleid = 4 and organization.partyid = ? and identity.partyid = partyrole.personpartyid");
		$stmtLoanIdentityCountDirector = $user_login->runQuery($sqlSelectIdentityCountDirector);
		$stmtLoanIdentityCountDirector->execute(array($row['applicantnumber']));
		$row3 = $stmtLoanIdentityCountDirector->fetch();
		$identityDirectorCount = $row3['identitycountd'];
		
		$sqlSelectDirectorCount = ("SELECT count(personpartyid) as directorcount FROM partyrole where roleid = 4 and partyrole.organizationpartyid = ?");
		$stmtLoanDirectorCount = $user_login->runQuery($sqlSelectDirectorCount);
		$stmtLoanDirectorCount->execute(array($row['applicantnumber']));
		$row4 = $stmtLoanDirectorCount->fetch();
		$DirectorCount = $row4['directorcount'];
		
		$sqlSelectAddressCount = ("SELECT count(physicaladdressid) as addresscount FROM physicaladdress where partyid = ? ");
		$stmtLoanAddressCount = $user_login->runQuery($sqlSelectAddressCount);
		$stmtLoanAddressCount->execute(array($row['applicantnumber']));
		$row5 = $stmtLoanAddressCount->fetch();
		$addressCount = $row5['addresscount'];
		
		echo '<tr>';
		echo '<td>'. $row['applicantnumber'] . '</td>';
		echo '<td>'. $row['title'].' '.$row['firstname'].' '.$row['othername'].' '.$row['lastname'] . '</td>';
		echo '<td>'. $row['dateofbirth'] . '</td>';
		echo '<td>'. $row['nationality'] . '</td>';		
		echo '<td>'. $row['maritalstatus'] . '</td>';		
		echo '<td>'. $row['createddate'] . '</td>';		
		echo "<td><a title='Add at least two Address residence and business' class='btn btn-success btn-circle' href='physical_address_add.php?id=" . $row['applicantnumber']."&wtd='addkits'"."> address ".$addressCount."</a>";		
		echo "<td><a title='Add at least one Identity' class='btn btn-success btn-circle' href='identity_add.php?id=" . $row['applicantnumber']."&wtd='addkits'".">Identity ".$identytyCount."</a>";
		echo "<td><a title='Add at least one Director' class='btn btn-success btn-circle ' href='person_details.php?id=" . $row['applicantnumber']."&wtd='addkits'".">Details</a>";
		if (($addressCount >= $addressNumber)&& ($identytyCount >= $identityNumber) ){
			echo "<td><a title='Request Loan' class='btn btn-success btn-sm active' href='loan_application_add.php?id=" . $row['applicantnumber'] ."&='addkits'".">Request Loan</a></td>";
		}else {
			echo "<td><a title='Request Loan' class='btn btn-danger btn-sm disabled' href='loan_application_add.php?id=" . $row['applicantnumber']."&wtd='addkits'"."> Add Requirement</a></td>";
		}		
		echo '</tr>';
	}
	
	
	
	?>
                                                                
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <!-- END EXAMPLE TABLE PORTLET-->                                          
                                            </div>
                                        </div>
									</div>
                                    <!-- END PAGE CONTENT INNER -->
                                </div>
                            </div>
                            <!-- END PAGE CONTENT BODY -->
                            <!-- END CONTENT BODY -->
                        </div>
                        <!-- END CONTENT -->
<?php include('../inc/footer.php'); ?>						