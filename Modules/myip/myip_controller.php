<?php
  /*
   All Emoncms code is released under the GNU Affero General Public License.
   See COPYRIGHT.txt and LICENSE.txt.

    ---------------------------------------------------------------------
    Emoncms - open source energy visualisation
    Part of the OpenEnergyMonitor project:
    http://openenergymonitor.org
  */

  // no direct access
  defined('EMONCMS_EXEC') or die('Restricted access');

  function myip_controller()
  {
    global $mysqli,$session, $route;

    $result = false;

    if (!isset($session['write'])) return array('content'=>false);

    if ($route->format == 'html')
    {
      if ($route->action == "list") $result = view("Modules/myip/iplist_view.php",array());
    }

    if ($route->format == 'json')
    {
      if ($route->action == 'get')
      {
        $userid = $session['userid'];
        $result = $mysqli->query("SELECT ipaddress FROM myip WHERE userid = '$userid';");
        $row = $result->fetch_object();
        $result = $row;
      }

      if ($route->action == 'set')
      {
        $userid = $session['userid'];
        $ip = getenv("REMOTE_ADDR");
        
        // Sanitation (an ip address is integers seperated by .'s)
        $parts = explode(".",$ip);
        foreach ($parts as $part) $part = (int) $part;
        $ip = implode(".",$parts);
        
        // Delete old ip
        $mysqli->query("DELETE FROM myip WHERE `userid`='$userid'");
        
        // Set new one
        $mysqli->query("INSERT INTO myip (`userid`,`ipaddress`) VALUES ('$userid','$ip')");
        
        // Provide verbose output
        $result = "IP address set to: $ip";
      }
    }


    return array('content'=>$result);
  }

?>
