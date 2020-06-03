<?php

namespace App\Http\Controllers;
 
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

use Airtable;
 
class AirTableController extends Controller
{

    public function getData(string $txt){
        $data     = Airtable::table('products')->get();
        $products = []; 
        $cc = 0;
        foreach($data as $product){
            $name     = isset($product['fields']['Name']) ? $product['fields']['Name'] : null; 
            $type     = isset($product['fields']['Product_type']) ? $product['fields']['Product_type'] : null; 
            $producer = isset($product['fields']['Producer']) ? $product['fields']['Producer'] : null; 
            $photo    = isset($product['fields']['Photo']) ? $product['fields']['Photo'] : null; 

            if(strstr($type, $txt) || strstr($name, $txt)){
                $products[$cc]['id']        = $product['id'];
                $products[$cc]['name']      = $name;
                $products[$cc]['type']      = $type;
                $products[$cc]['producer']  = $producer;
                $products[$cc]['photo']     = $photo[0]['url'];
                $cc++;

                if($cc >= 5) break;
            }
        }
        
        return response()->json(['data' => count($products) > 0 ? $products : null]);
    }


}