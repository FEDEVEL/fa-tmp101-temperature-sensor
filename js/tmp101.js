//This function will read temperature of OpenRex board by accessing to tmp101.php file and reading TMP101 sensor value
function fa_access_tmp101()
{
        //read IP address and units from our hidden elements created in [shortcode] section of the fa-tmp101-temperature-sensor.php file
        var openrex_ip_address  = document.getElementById("fa_openrex_ip").value; //this will take the value from our hidden input element which has id=fa_openrex_ip (we created it in fa-tmp101-temperature-sensor.php)
        var temperature_units  = document.getElementById("fa_tmp101_units").value; //this will take the value from our hidden input element which has id=fa_tmp101_units (we created it in fa-tmp101-temperature-sensor.php)
 
        //prepare variables
        var temperature_value = document.getElementById("fa_tmp101"); //this will create a "pointer" to our <span id="fa_tmp101"></span>, so we can replace its content
        var url = 'http://'+openrex_ip_address+'/wp-content/plugins/fa-tmp101-temperature-sensor/tmp101.php?units='+temperature_units; //url of our tmp101.php file
 
        //this will call tmp101.php file to read the sensor
        request = jQuery.ajax({
                dataType: "json",
                url: url,
        });
 
        //here we will proceed the answer from tmp101.php file
        request.done (function( data ) { //this will be executed when we get an answer from tmp101.php
                //console.log("Done"); //debug output, you can see this in console output (e.g. in Chrome use Right Click -> Inspect element -> Console)
                //console.log(data); //debug output
 
                if (data['valid']==='true')
                {
                        //replace <span id="fa_tmp101"></span> content with the temperature and units
                        temperature_value.innerHTML =  data['openrex']['sensors']['tmp101']['temperature']+' '+data['openrex']['sensors']['tmp101']['units'];
                        return true;
                }
                else
                {
                        //replace <span id="fa_tmp101"></span> content with the error message
                        temperature_value.innerHTML = data['error_msg'];
                        return false;
                }
        });
 
        request.fail (function( data ) { //this will be executed if we will not get answer from tmp101.php
                //console.log("Failed"); //debug output
                //replace <span id="fa_tmp101"></span> content with "Connection failed" message
                temperature_value.innerHTML = 'Connection failed';
                return false;
        });
}
