<?php
/*
Example of the URL address for this file is http://openrex_IP_address/wp-content/plugins/fa-tmp101-temperature-sensor/tmp101.php?units=celsius
*/
  
$temperature_units = empty($_GET['units'])?'celsius':$_GET['units']; //get temperature units from url, if empty, use celsius
 
//Run i2cget Linux command to read temperature from the onboard TMP101 sensor
$ret = exec('sudo i2cget -f -y 1 0x48 0x00 w',$out,$err);

//THIS IS VERY IMPORTANT. IF YOU WOULD LIKE TO RUN A LINUX COMMAND, YOU NEED TO SET PROPER PERMISSIONS!
//One way how to do it, is this (as an example we will use i2cget command):
//1) Check under what user the webserver is running, e.g. run "ps aux | egrep '(apache|httpd)'" (in our case it is "daemon")
//2) Run "visudo" and add following lines at the end of the file. Specify the applications and resources which the "exec" command will be using: 
//	daemon ALL=(ALL:ALL)  NOPASSWD: /usr/sbin/i2cget
//	daemon ALL=(ALL:ALL)  NOPASSWD: /dev/i2c-1
//NOTE:
//If your command doesn't work, instead of "$ret = exec('sudo i2cget -f -y 1 0x48 0x00 w',$out,$err)" try to use the following line:
//passthru("sudo i2cget -f -y 1 0x48 0x00 w  >> /tmp/my_plugin.log 2>&1 &");
//Then run 'cat /tmp/my_plugin.log'. You will see there details about why it's failing.
//The "passthru" command can also be used if you don't want to wait until a command exection is finished.

//based on TMP101 datasheet, we need to calculate the temperature:
$ret =  hexdec(str_replace('0x', '', $ret)); //remove 0x from begining and convert it to DEC
$temperature = (($ret & 0x00FF)<<4) | (($ret & 0xF000)>>12);  //swap the bytes and shift them by 4 to the right
if ($temperature & 0x0800)
    $temperature = 0 - (0x07FF & (~$temperature)) * 0.0625; //if temperature is negative (below 0 Celsius)
else
    $temperature = $temperature *  0.0625; //if temperature is positve (above 0 Celsius)
 
//if we would like the answer from tmp101.php to be in Fahrenheit, convert the temperature
if ($temperature_units != 'celsius')
    $temperature = sprintf("%0.2f",$temperature * 9 / 5 + 32);
 
//prepare the answer
$result = array(
    'openrex' =>  array(
        'sensors' => array(
            'tmp101' => array(
                'units' => ($temperature_units == 'celsius')?' °C':' °F',
                'temperature' => $temperature
            ),              
        ),
    ),
    'valid' => 'true',
    'error_msg' => '',
);
  
$response = json_encode( $result); //this will transfer our result array into JSON
echo $response; //this will send the response to our javascript
?>
