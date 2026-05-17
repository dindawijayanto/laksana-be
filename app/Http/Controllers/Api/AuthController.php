    <?php

    namespace App\Http\Controllers\Api;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use App\Models\User;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Validator;

    class AuthController extends Controller
    {
        /**
         * FITUR 1: REGISTRASI USER/WARGA BARU
         */
        public function register(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'name'     => 'required|string|max:255',
                'email'    => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'user',
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type'   => 'Bearer',
                'user'         => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'role'  => $user->role,
                ]
            ]);
        }

        /**
         * FITUR 2: LOGIN SISTEM
         */
        public function login(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'email'    => 'required|string|email',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Email atau password yang Anda masukkan salah.'
                ], 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message'      => 'Selamat datang kembali, ' . $user->name,
                'access_token' => $token,
                'token_type'   => 'Bearer',
                'user'         => $user
            ], 200);
        }

        /**
         * FITUR 3: LOGOUT / HAPUS SESI TOKEN
         */
        public function logout(Request $request)
        {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Berhasil keluar dari sistem Laksana.'
            ], 200);
        }

        /**
         * FITUR 4: UPDATE PROFILE USER
         * Endpoint: POST /api/profile
         */
        public function updateProfile(Request $request)
        {
            $user = $request->user();

            $validator = Validator::make($request->all(), [
                'name'     => 'sometimes|required|string|max:255',
                'email'    => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
                'password' => 'sometimes|nullable|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors()->first(),
                    'errors'  => $validator->errors()
                ], 422);
            }

            if ($request->filled('name')) {
                $user->name = $request->name;
            }

            if ($request->filled('email')) {
                $user->email = $request->email;
            }

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            return response()->json([
                'message' => 'Profil berhasil diperbarui!',
                'user'    => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'role'  => $user->role,
                ]
            ], 200);
        }
    }