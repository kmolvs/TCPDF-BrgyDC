<?php

require_once('library/tcpdf.php');
include("../../settings-db/config.php");
session_start();
if(!isset($_SESSION['user']))
{   
    echo "<script>						
    window.location.href='404.php ';
    </script>";
}
$session = $_SESSION['user'];

$id = $_GET['id'];

$UpdateDeleteQuery = "UPDATE clearance_endorsement SET ModifyByUserAccountId = '$session', Status = 'Done'  WHERE id = '$id'";

    if ($conn->query($UpdateDeleteQuery) === TRUE) 
    {
     
            $InsertActivityLogsQuery = "INSERT INTO activity_logs(UserAccountId, Message, Type, Category, Status, IsRead, CreatedOn) 
            VALUES(
            (SELECT id from user_account WHERE id = '$session'),
            'Clearance Endorsement Done',
            'Clearance Endorsement Done',
            'Admin',
            'Added',
            0,
            CURRENT_TIMESTAMP()
            )
            ";
        
            if ($conn->query($InsertActivityLogsQuery) === TRUE) 
            {


                $SelectUserCensusQuery = 
                            
                "
                SELECT
                ce.id,
                ce.Recipient,
                ce.Address,
                ce.ContactPerson,
                ce.NameOfClient,
                ce.Age,
                ce.Sex,
                ce.ContactNumber,
                ce.ClientAddress,
                ce.NameOfFamily,
                ce.FamilyContactNumber,
                ce.FamilyAddress,
                ce.Reason,
                ce.Service,
                DATE_FORMAT(ce.CreatedOn, '%M %d, %Y') AS CreatedOn,
                CONCAT(crl.FirstName, ' ', crl.MiddleName, ' ', crl.LastName) AS FullName,
                crl.Address AS UserAddress,
                ua.PhoneNumber,
                ua.Email,
                CONCAT(cl.FirstName, ' ', cl.MiddleName, ' ', cl.LastName) AS EMFullName
                FROM
                clearance_endorsement ce
                INNER JOIN
                user_account ua
                ON
                ce.CreatedByUserAccountId = ua.id
                LEFT JOIN
                census_resident_list crl
                ON
                crl.id = ua.CensusId
                LEFT JOIN
                census_resident_list cl 
                ON
                cl.id = crl.EmergencyContactId
                WHERE
                ce.id = '$id'
                AND
                ce.IsDeleted = '0'

                ";

                $ResultCensusQuery = $conn->query($SelectUserCensusQuery);

                    if ($ResultCensusQuery->num_rows > 0) 
                    {
                            while($row = $ResultCensusQuery->fetch_assoc()) 
                            {
                                
                                $Recipient = $row['Recipient'];
                                $Address = $row['Address'];
                                $ContactPerson = $row['ContactPerson'];
                                $NameOfClient = $row['NameOfClient'];
                                $Age = $row['Age'];
                                $Sex = $row['Sex'];
                                $ContactNumber = $row['ContactNumber'];
                                $ClientAddress = $row['ClientAddress'];
                                $NameOfFamily = $row['NameOfFamily'];
                                $FamilyContactNumber = $row['FamilyContactNumber'];
                                $FamilyAddress = $row['FamilyAddress'];
                                $Reason = $row['Reason'];
                                $Service = $row['Service'];
                                $CreatedOn = $row['CreatedOn'];
                                $FullName = $row['FullName'];
                                $UserAddress = $row['UserAddress'];
                                $PhoneNumber = $row['PhoneNumber'];
                                $Email = $row['Email'];
                                $EMFullName = $row['EMFullName'];
                                $Cid = $row['id'];


                $SelectUserCensusQuery = 
                            
                "
                SELECT 
                CONCAT(crl.FirstName, ' ', crl.MiddleName, ' ', crl.LastName) AS CaptainName, 
                bo.Position 
                FROM 
                census_resident_list crl 
                INNER JOIN 
                barangay_officials bo 
                ON 
                crl.id = bo.CensusId 
                WHERE 
                bo.Position ='Barangay Chairman'
                ";

                $ResultCensusQuery = $conn->query($SelectUserCensusQuery);

                    if ($ResultCensusQuery->num_rows > 0) 
                    {
                            while($row = $ResultCensusQuery->fetch_assoc()) 
                            {

                                $CaptainName = $row['CaptainName'];
                                $Position = $row['Position'];
                               
                
class MYPDF extends TCPDF {

  
    public function Header() {
        
        $image_file = 'header.jpg';
        $this->Image($image_file, 5, 5, 200, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
/*
        $image_file1 = 'cavite.jpg';
        $this->Image($image_file1, 30, 97, 160, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
*/
        $this->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 50000, 'color' => array(0, 0, 255)));
        $this->Line(5, 5, $this->getPageWidth()-5, 5); 
        $this->Line($this->getPageWidth()-5, 5, $this->getPageWidth()-5,  $this->getPageHeight()-5);
        $this->Line(5, $this->getPageHeight()-5, $this->getPageWidth()-5, $this->getPageHeight()-5);
        $this->Line(5, 5, 5, $this->getPageHeight()-5);

        
    }

}


$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Barangay Malagasang 1-G');
$pdf->SetTitle('Clearance Endorsement - '.$Recipient.'');
$pdf->SetSubject('Clearance Endorsement');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

$pdf->SetFont('times', '', 12);
$pdf->AddPage();

$html = '
<br><br><br><br><br><br><br><br>
<div style="text-align:center;"><h1>BARANGAY VAWC DESK REFERRAL FORM</h1></div>
<br>
<br>
Case No.: &nbsp;<b>'.$Cid.'</b>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;

Date Referral: <b>'.$CreatedOn.'</b>
<br>
<br>
<br>
<br>
Address:<b> '.$Address.' </b><br>
Contact Person:<b> '.$ContactPerson.' </b><br>
Name of Client:<b> '.$NameOfClient.' </b><br><br>
Age:<b> '.$Age.' </b>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
Sex:<b> '.$Sex.' </b>
&nbsp;&nbsp;&nbsp;&nbsp;
Address:<b> '.$ClientAddress.' </b><br>
Name of Family/Guardian:<b> '.$NameOfFamily.' </b>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
Contact No.:<b> '.$FamilyContactNumber.' </b><br>
Address:<b> '.$FamilyAddress.' </b><br><br>
Reason/s for Refeerral:<b> '.$Reason.' </b><br>
Specific Service/s Requested:<b> '.$Service.' </b><br><br><br>

<b>Please refer to attached report/intake form case summary for more information.
        Feedback  requested and send to Referring Party/Agency:</b><br><br>
<b>Barangay Malagasang 1-G</b><br><br>
Address:<b> '.$UserAddress.' </b><br>
Phone No.:<b> '.$PhoneNumber.' </b><br>
Email Address:<b> '.$Email.' </b><br>
Contact Person:<b> '.$FullName.' </b><br>
<br><br><br>

<b>Referred by:</b><br><br><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

'.$FullName.'


&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<b>HON. '.$CaptainName.'</b>

<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
VAWC Desk Officer

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;
<b>'.$Position.'</b>

';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('Clearance-Endorsement-of-'.$Recipient.'.pdf', 'I');

}
         }
                }
            }

        }
        else 
        {
        echo "Error: " . $InsertActivityLogsQuery . "<br>" . $conn->error;
        }
        }	  
        else 
        {
        echo "Error: " . $UpdateDeleteQuery . "<br>" . $conn->error;
        }
?>
