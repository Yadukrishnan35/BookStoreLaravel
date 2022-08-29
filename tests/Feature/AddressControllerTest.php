<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddressControllerTest extends TestCase
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

    /**
     * @test for
     * Address add to respective user successfull
     *
     */

    public function test_SuccessfulAddAddress()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjYxMjIyMzA3LCJleHAiOjE2NjEyMjU5MDcsIm5iZiI6MTY2MTIyMjMwNywianRpIjoiMFdxcFBnSVJzWkxpOTFUbyIsInN1YiI6IjIiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.EQpamOeaC0RHllTiIei7GBXRvlrisuR_rCVMHQRT-cL'
        ])->json('POST', '/api/addAddress', [
            "address" => "Kochi",
            "city" => "ekm",
            "state" => "kerala",
            "landmark" => "near beach",
            "pincode" => "98765",
            "address_type" => "home",
        ]);
        $response->assertStatus(201);
    }

    public function test_UnSuccessfulAddAddress()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjUwMDMzMDkwLCJleHAiOjE2NTAwMzY2OTAsIm5iZiI6MTY1MDAzMzA5MCwianRpIjoiT2RSRkEybU9DbGVzTkpGQiIsInN1YiI6IjIiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.VVZe9Jvto9Y15k60AQwcIdsxesrakO_BbfvFRPRjkol'
        ])->json('POST', '/api/addAddress', [
            "address" => "Kochi",
            "city" => "ekm",
            "state" => "Kerala",
            "landmark" => "near rly stn",
            "pincode" => "9",
            "address_type" => "home",
        ]);
        $response->assertStatus(200);
    }

    /**
     * @test for
     * Address Update to respective user successfull
     *
     */
    public function test_SuccessfulUpdateAddress()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjUwMDMzMDkwLCJleHAiOjE2NTAwMzY2OTAsIm5iZiI6MTY1MDAzMzA5MCwianRpIjoiT2RSRkEybU9DbGVzTkpGQiIsInN1YiI6IjIiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.VVZe9Jvto9Y15k60AQwcIdsxesrakO_BbfvFRPRjkol'
        ])->json('POST', '/api/updateAddress', [
            "id" => "4",
            "address" => "Kochi",
            "city" => "ekm",
            "state" => "Kerala",
            "landmark" => "near rly",
            "pincode" => "98776",
            "address_type" => "home",
        ]);
        $response->assertStatus(200);
    }

    /**
     * @test for
     * Address Update to respective user Unsuccessfull
     *
     */
    public function test_UnSuccessfulUpdateAddress()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjUwMDMzMDkwLCJleHAiOjE2NTAwMzY2OTAsIm5iZiI6MTY1MDAzMzA5MCwianRpIjoiT2RSRkEybU9DbGVzTkpGQiIsInN1YiI6IjIiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.VVZe9Jvto9Y15k60AQwcIdsxesrakO_BbfvFRPRjkol'
        ])->json('POST', '/api/updateAddress', [
            "id" => "25",
            "address" => "Kochi",
            "city" => "ekm",
            "state" => "Kerala",
            "landmark" => "near rly",
            "pincode" => "98776",
            "address_type" => "home",
        ]);
        $response->assertStatus(201);
    }

    /**
     * @test
     * for delete address successfull
     */
    public function test_SuccessfullDeleteAddress()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjYxMjIyMzA3LCJleHAiOjE2NjEyMjU5MDcsIm5iZiI6MTY2MTIyMjMwNywianRpIjoiMFdxcFBnSVJzWkxpOTFUbyIsInN1YiI6IjIiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.EQpamOeaC0RHllTiIei7GBXRvlrisuR_rCVMHQRT-gY'
        ])->json(
            'POST',
            '/api/deleteAddress',
            [
                "id" => 3,
            ]
        );
        $response->assertStatus(200);
    }

    /**
     * @test for successfull display all Address
     * for respective user
     */
    public function test_SuccessfullDisplayAddress()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjYxMjIyMzA3LCJleHAiOjE2NjEyMjU5MDcsIm5iZiI6MTY2MTIyMjMwNywianRpIjoiMFdxcFBnSVJzWkxpOTFUbyIsInN1YiI6IjIiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.EQpamOeaC0RHllTiIei7GBXRvlrisuR_rCVMHQRT-gY'
        ])->json(
            'GET',
            '/api/getAddress',
            []
        );
        $response->assertStatus(201);
    }

    /**
     * @test for Unsuccessfull display all Address
     * for respective user
     */
    public function test_UnSuccessfullDisplayAddress()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjUwMDMzMDkwLCJleHAiOjE2NTAwMzY2OTAsIm5iZiI6MTY1MDAzMzA5MCwianRpIjoiT2RSRkEybU9DbGVzTkpGQiIsInN1YiI6IjIiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.VVZe9Jvto9Y15k60AQwcIdsxesrakO_BbfvFRPRj-gY'
        ])->json(
            'GET',
            '/api/getAddress',
            []
        );
        $response->assertStatus(404);
    }
}