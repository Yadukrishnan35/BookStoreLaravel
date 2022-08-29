<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function successfulPlaceOrderTest()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjYxMjIyMzA3LCJleHAiOjE2NjEyMjU5MDcsIm5iZiI6MTY2MTIyMjMwNywianRpIjoiMFdxcFBnSVJzWkxpOTFUbyIsInN1YiI6IjIiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.EQpamOeaC0RHllTiIei7GBXRvlrisuR_rCVMHQRT-cL'

        ])
            ->json('POST', '/api/placeorder', [
                "cart_id" => "9",
                "address_id" => "1",
                
            ]);
        $response->assertStatus(201);
    }

    /**
     * UnSuccessfull Place Order of an User
     * This test is to Place the Order
     * by using cart_id and address_id as credentials
     * Using Wrong Credentials for unsuccessful test
     * 
     * @test
     */
    public function unSuccessfulPlaceOrderTest()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1J9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjYxMjIyMzA3LCJleHAiOjE2NjEyMjU5MDcsIm5iZiI6MTY2MTIyMjMwNywianRpIjoiMFdxcFBnSVJzWkxpOTFUbyIsInN1YiI6IjIiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.EQpamOeaC0RHllTiIei7GBXRvlrisuR_rCVMHQRT-cL'

        ])
            ->json('POST', '/api/placeorder', [
                "cart_id" => "8",
                "address_id" => "1",
               
            ]);
        $response->assertStatus(200);
    }

    /**
     * Successfull Cancel Order of an User
     * This test is to Cancel the Order
     * by using order_id as credentials
     * 
     * @test
     */
    public function successfulCancelOrderTest()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjYxMjIyMzA3LCJleHAiOjE2NjEyMjU5MDcsIm5iZiI6MTY2MTIyMjMwNywianRpIjoiMFdxcFBnSVJzWkxpOTFUbyIsInN1YiI6IjIiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.EQpamOeaC0RHllTiIei7GBXRvlrisuR_rCVMHQRT-cL'

        ])
            ->json('POST', '/api/cancelorder', [
                "order_id" => "w4nV0M1Lc",
                
            ]);
        $response->assertStatus(200);
    }

    /**
     * UnSuccessfull Cancel Order of an User
     * This test is to Cancel the Order
     * by using order_id as credentials
     * Using Wrong Credentials for unsuccessful test
     * 
     * @test
     */
    public function unSuccessfulCancelOrderTest()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjYxMjIyMzA3LCJleHAiOjE2NjEyMjU5MDcsIm5iZiI6MTY2MTIyMjMwNywianRpIjoiMFdxcFBnSVJzWkxpOTFUbyIsInN1YiI6IjIiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.EQpamOeaC0RHllTiIei7GBXRvlrisuR_rCVMHQRT-cL'

        ])
            ->json('POST', '/api/cancelorder', [
                "order_id" => "w4nV0M1Lc",
                
            ]);
        $response->assertStatus(200);
    }
}