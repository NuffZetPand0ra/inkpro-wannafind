<?php
/**
 * Communication with wannafinds hostedshop API
 * 
 * @package inkpro\mailRemarketing
 */
namespace inkpro\mailRemarketing;
/**
 * Wrapper class for communication with the hostedshop API.
 * 
 * Documentation for the API can be found here: https://api.hostedshop.dk/doc/
 * Hostedshop uses a soap client to pool from their servers.
 * 
 * Make sure you have set the WANNAFIND_USER and WANNAFIND_PASS vars in the .env file.
 * 
 * To initiate, use `$wf = new \inkpro\mailRemarketing\Wannafind();`.
 * 
 * @author Esben Tind <esben@inkpro.dk>
 * @see https://api.hostedshop.dk/doc/
 */
class Wannafind{

    /** @var \SoapClient The SOAP client. */
    protected static $client = null;
    /** @var object[]|false Contains the response from API after receiving all users. Indexed by user id. */
    public $allUsers = false;
    /** @var object[]|false Contains the response from API after receiving all orders. */
    public $orders = false;

    

    /**
     * Envokes the soap client with wannafind login details.
     * 
     * @return bool True if soap client successfully connected. Throws exception on error.
     */
    private function __construct(){
        $client = new \SoapClient('https://api.hostedshop.dk/service.wsdl');
        $client->Solution_Connect(array(
            'Username'=>$_ENV['WANNAFIND_USER'],
            'Password'=>$_ENV['WANNAFIND_PASS']
        ));
        $this->client = $client;
        $this->callApi("User_SetFields",array("Fields"=>"Id,Firstname,Lastname,Email"));
        $this->callApi("Order_SetFields",array("Fields"=>"Id,OrderLines,CustomerId"));
        return true;
    }

    public static function Client(){
        if(self::$client == null){
            self::$client = new Wannafind();
        }
        return self::$client;
    }

    /**
     * Internal method for handling API calls. All API calls should use this.
     * 
     * @param string $call Which API call to execute, i.e. "User_GetAll".
     * @param array $settings Settings to pass to the API.
     * @return mixed The response from the API.
     */
    private function callApi(string $call, array $settings=array()){
        // TODO: Remove response logic, and just return the API response.
        $response = $this->client->$call($settings);
        $responseName = $call."Result";
        switch(true){
            case is_bool($response->$responseName):
                return $response->$responseName;
                break;
            default:
                return $response->$responseName->item;
                break;
        }
    }

    /**
     * Gets all users
     * 
     * @return object[] Array with all the users.
     */
    function getUsers(){
        if($this->allUsers) return $this->allUsers;
        $response = $this->callApi("User_GetAll");
        foreach($response as $user){
            $this->allUsers[$user->Id] = $user;
        }
        return $this->allUsers;
    }

    /**
     * Receives all orders from the last 1 day.
     * 
     * @return object[] Array with the orders.
     */
    function getOrders(){
        if($this->orders) return $this->orders;
        $now = new \DateTime();
        $then = clone $now;
        $goingBack = new \DateInterval("P1D");
        // Sorter fra hvis de er gratis
        $dateFormat = "Y-m-d";
        $then->sub($goingBack);
        $options = array(
            "Start"=>$then->format($dateFormat),
            "End"=>$now->format($dateFormat),
            "Status"=>"1,2,3,4,6,7,8"
        );
        return $this->orders = $this->callApi("Order_GetByDate",$options);
    }
    
    /**
     * Gets all orders made by a certain user.
     * 
     * @param int $userId Id of the user to fetch orders from.
     */
    function getUsersOrders(int $userId){
        return $this->callApi("Order_GetByDateAndUser",array(
            "UserId"=>$userId,
            "Start"=>null,
            "End"=>null
        ));
    }

    function getOrderLines(int $orderId){
        return $this->callApi("Order_GetLines");
    }

    /**
     * Gets all images linked to a product id.
     * 
     * @param int $productId Id of the product.
     * @param string $prefix To prefix the file path. Should be the directory where images are stored.
     * @return object[] Array of stdObjects containing data about the image.
     */
    function getProductImages(int $productId, string $prefix='https://shop1434.hstatic.dk/upload_dir/shop/'){
        $images = $this->callApi("Product_GetPictures",array(
            "ProductId"=>$productId
        ));
        if(!is_array($images)){
            $images = array($images);
        }
        foreach($images as &$image){
            $image->FilePath = $prefix.$image->FileName;
        }
        return $images;
    }
}
?>