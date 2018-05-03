# Wrapper class for communication with the hostedshop API.

Documentation for the API can be found here: https://api.hostedshop.dk/doc/  
Hostedshop uses a soap client to pool from their servers.

Make sure you have set the WANNAFIND_USER and WANNAFIND_PASS vars in the environment.  
To initiate, use `$wf = new \inkpro\wannafind\Wannafind();`.