<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public function validateUniqueValue($table, $field, $value, $id = NULL)
    {
        $data = [$field => $value];

        if($id){
            $validate = Validator::make($data, [
                $field => "required|unique:$table,$field,$id"
            ]);

        }
        else{
            $validate = Validator::make($data, [
                $field => "required|unique:$table,$field"
            ]);
        }

        if($validate->fails()){
            return ['status' => 422, 'isUnique' => false ,'message' => 'This value is already taken. Choose another one'];
        }

        return ['status' => 200, 'isUnique' => true  ,'message' => 'This value is ok'];
    }
}