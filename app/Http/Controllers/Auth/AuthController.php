<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 15, // agar tidak menggantung terlalu lama
        ]);
    }

    // Endpoint untuk login yang diarahkan ke auth-service
    public function login(Request $request)
    {
        $maxRetries = 5;
        $retryDelayMs = 300;

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $response = $this->client->get(env('AUTH_SERVICE_URL') . '/auth-service', [
                    'json' => $request->all(),
                ]);

                $data = json_decode($response->getBody(), true);
                
                if (isset($data['token'])) {

                    $userData = $data['user'];

                    if (!$userData) {
                        return response()->json(['error' => 'User not found'], 404);
                    }

                    $user = User::where('employee_id', $userData['employee_id'])->first();

                    Auth::login($user, $request->boolean('remember'));

                    $request->session()->put('user', [
                        'id'    => $user['id'],
                        'name'  => $user['name'],
                        'email' => $user['email'],
                        'employee_id' => $user['employee_id'],
                    ]);

                    
                    $request->session()->put('system', 'facecard');
                    $request->session()->regenerate();

                    return redirect('facecard');
                }

            } catch (\Exception $e) {
                Log::error('Unexpected login error: ' . $e->getMessage());
                return redirect('/login');
            }
        }
    }
}
