<?php
if (file_exists('C:\Users\Andre\Documents\GitHub\tina4php\public_html/includes/v1.0.0.0/Publicapi.php')) { require_once ('C:\Users\Andre\Documents\GitHub\tina4php\public_html/includes/v1.0.0.0/Publicapi.php'); }
class Public_int extends cde_interface {
    function get_interface ($CDE, $go, $request, $cdedev) {
      $DEV = $cdedev->DEV;
      //iterate values into $html variable to return things from the interface
      //overload the header and footer function to get your own header and footer going.
      //Andre: The headers here allow for things to automate your html headers with the includes needed
$html .= $this->html_header ("TINA4 - This Is Not A 4ramework ", "Publiccss");
$html .= $this->html_ajaxhandler();

$html .= h1( "Welcome to TINA4" );

$li[] = li ( a (array ("target" => "_blank", "href" => "?interface=Developer" ), "Login to the development environment using email: admin & password: admin") );
$li[] = li ( a (array ("target" => "_blank", "href" => "help.php" ), "Help for using the Shape Language" ) );
$li[] = li ( a (array ("target" => "_blank", "href" => "?interface=Example" ), "An Example Application using TINA 4" ) );


$html .= ul ( $li );

//Andre: This footer is for debugging, the first parameter displays session and request information, the second is to refresh SASS without reloading the screen.
$html .= $this->html_footer(true, true);
    return $html;
  }
}
?>