Aquí tienes el manual de implementación estructurado en formato Markdown. Está diseñado con explicaciones claras y bloques de código listos para que tu IA del IDE pueda interpretarlos e integrarlos en tu proyecto.

---

# Manual de Implementación: Google Sign-In (Angular 17+ y PHP 8+)

Este documento detalla la integración de autenticación de Google utilizando un flujo OAuth 2.0 y OpenID Connect, conectando un frontend en Angular con un backend en PHP que emite su propio JSON Web Token (JWT).

## 1. Requisitos y Dependencias

Para la implementación completa, necesitarás instalar las siguientes dependencias en tus respectivos entornos:

### Frontend (Angular)

* 
**Librería principal:** Instala `@abacritt/angularx-social-login` (versión >=2.2) para el botón de Google y la gestión de la sesión OAuth.


* 
**Decodificador (opcional):** Instala `jwt-decode` para leer el payload del JWT en el cliente.



```bash
npm install @abacritt/angularx-social-login jwt-decode

```

### Backend (PHP)

* 
**Manejo de JWT:** Instala `firebase/php-jwt` (versión >=6.0) para codificar tus propios tokens y decodificar el ID Token de Google. Esta librería instala automáticamente `phpseclib/phpseclib`.


* 
**Variables de entorno:** Instala `vlucas/phpdotenv` para cargar credenciales de forma segura.



```bash
composer require firebase/php-jwt vlucas/phpdotenv

```

---

## 2. Configuración en Google Cloud Console

Antes de programar, la IA debe saber que necesitas las credenciales de Google.

1. Accede a Google Cloud y crea un proyecto.


2. Activa la API **Google Identity Toolkit API**.


3. Configura la **Pantalla de consentimiento de OAuth** (con los scopes: `openid`, `email`, `profile`).


4. Crea credenciales de tipo **ID de cliente de OAuth** (Aplicación web).


5. Añade los orígenes autorizados, por ejemplo, `http://localhost:4200` para desarrollo.


6. Obtén y guarda el `GOOGLE_CLIENT_ID` y `GOOGLE_CLIENT_SECRET`.



---

## 3. Implementación del Backend (PHP)

El backend será responsable de verificar el token de Google, gestionar el acceso a la base de datos y emitir un JWT interno.

### 3.1 Variables de Entorno (`.env`)

Configura las variables de entorno sin exponerlas en el control de versiones. Necesitas definir un secreto JWT de al menos 32 caracteres.

```env
GOOGLE_CLIENT_ID=tu_cliente_id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=tu_secreto_google
JWT_SECRET=una_cadena_aleatoria_muy_larga_y_segura_minimo_32_chars
JWT_EXPIRATION=86400
URL_FRONTEND_ORIGIN=http://localhost:4200

```

### 3.2 Configuración CORS (`index.php`)

Es crítico configurar CORS para permitir peticiones desde Angular.

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowedOrigins = [$_ENV['URL_FRONTEND_ORIGIN']];

if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: $origin");
}
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Auth-Token");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

```

### 3.3 Verificación y Endpoint de Login (`api/auth/google`)

El backend debe descargar las claves públicas (JWKS) de Google y validar los claims (`iss`, `aud`, `exp`). Si es válido, emite un JWT firmado con `HS256`.

```php
<?php
use Firebase\JWT\JWT;
use Firebase\JWT\JWK;

function verificarIdTokenGoogle(string $idToken, string $clientId): ?array {
    $jwksUrl = 'https://www.googleapis.com/oauth2/v3/certs';
    $ch = curl_init($jwksUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $keysJson = curl_exec($ch);
    curl_close($ch);
    
    if (!$keysJson) return null;
    
    $keySet = JWK::parseKeySet(json_decode($keysJson, true));
    
    try {
        $decoded = (array) JWT::decode($idToken, $keySet);
        $validIssuers = ['https://accounts.google.com', 'accounts.google.com'];
        if (!in_array($decoded['iss'], $validIssuers)) return null;
        if ($decoded['aud'] !== $clientId) return null;
        if ($decoded['exp'] < time()) return null;
        return $decoded;
    } catch (Exception $e) {
        return null;
    }
}

// Endpoint POST /api/auth/google
header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true);
$idToken = $input['token'] ?? null;

if (!$idToken) {
    http_response_code(400);
    echo json_encode(['error' => 'Token requerido']);
    exit;
}

$payload = verificarIdTokenGoogle($idToken, $_ENV['GOOGLE_CLIENT_ID']);
if (!$payload) {
    http_response_code(401);
    echo json_encode(['error' => 'Token de Google inválido']);
    exit;
}

// Lógica de base de datos aquí (buscar o crear usuario)
$email = $payload['email'];
$nombre = $payload['name'] ?? '';

$iat = time();
$jwtPayload = [
    'iat' => $iat,
    'exp' => $iat + (int)$_ENV['JWT_EXPIRATION'],
    'data' => ['email' => $email, 'nombre' => $nombre]
];

$jwtInterno = JWT::encode($jwtPayload, $_ENV['JWT_SECRET'], 'HS256');

echo json_encode(['status' => 'success', 'token' => $jwtInterno]);

```

### 3.4 Middleware de Protección de Rutas

Valida el JWT interno recibido en el header `Authorization`.

```php
<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function validarJWT(): array {
    $headers = getallheaders();
    $auth = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    
    if (str_starts_with($auth, 'Bearer ')) {
        $token = substr($auth, 7);
    }

    if (empty($token)) {
        http_response_code(401);
        echo json_encode(['error' => 'Token no proporcionado']);
        exit;
    }

    try {
        $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
        return (array) $decoded->data;
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['error' => 'Token inválido o expirado']);
        exit;
    }
}

```

---

## 4. Implementación del Frontend (Angular)

El frontend mostrará el botón oficial, recibirá el ID Token de Google y lo enviará al backend.

### 4.1 Configuración Base (`app.config.ts`)

Configura el proveedor con tu Client ID público .

```typescript
import { ApplicationConfig } from '@angular/core';
import { SocialLoginModule, SocialAuthServiceConfig, GoogleLoginProvider } from '@abacritt/angularx-social-login';
import { provideHttpClient, withInterceptors } from '@angular/common/http';
import { tokenInterceptor } from './token.interceptor';

export const appConfig: ApplicationConfig = {
  providers: [
    provideHttpClient(withInterceptors([tokenInterceptor])),
    {
      provide: 'SocialAuthServiceConfig',
      useValue: {
        autoLogin: false,
        providers: [
          {
            id: GoogleLoginProvider.PROVIDER_ID,
            provider: new GoogleLoginProvider('TU_GOOGLE_CLIENT_ID')
          }
        ]
      } as SocialAuthServiceConfig,
    }
  ]
};

```

### 4.2 Servicio de Autenticación (`auth.service.ts`)

Envía el token al backend y gestiona el JWT resultante .

```typescript
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class AuthService {
  private apiUrl = 'http://localhost:8000/api'; // Ajustar URL

  constructor(private http: HttpClient) {}

  loginConGoogle(idToken: string): Observable<{ token: string }> {
    return this.http.post<{ token: string }>(`${this.apiUrl}/auth/google`, { token: idToken });
  }

  estaAutenticado(): boolean {
    const token = localStorage.getItem('token');
    if (!token) return false;
    try {
      const payload = JSON.parse(atob(token.split('.')[1]));
      return payload.exp > (Date.now() / 1000);
    } catch {
      return false;
    }
  }

  cerrarSesion(): void {
    localStorage.removeItem('token');
  }
}

```

### 4.3 Componente Login (`login.component.ts` y `.html`)

Captura el flujo de inicio de sesión de Google .

**Template HTML:**

```html
<asl-google-signin-button type="standard" size="large" shape="pill" width="300"></asl-google-signin-button>

```

**TypeScript:**

```typescript
import { Component, OnInit, OnDestroy } from '@angular/core';
import { Router } from '@angular/router';
import { SocialAuthService, SocialUser } from '@abacritt/angularx-social-login';
import { AuthService } from './auth.service';
import { Subscription } from 'rxjs';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html'
})
export class LoginComponent implements OnInit, OnDestroy {
  private sub!: Subscription;

  constructor(
    private socialAuthService: SocialAuthService,
    private authService: AuthService,
    private router: Router
  ) {}

  ngOnInit() {
    this.sub = this.socialAuthService.authState.subscribe((user: SocialUser) => {
      if (user?.idToken) {
        this.authService.loginConGoogle(user.idToken).subscribe({
          next: (res) => {
            localStorage.setItem('token', res.token);
            this.router.navigate(['/dashboard']);
          },
          error: (err) => console.error('Error al hacer login', err)
        });
      }
    });
  }

  ngOnDestroy() {
    if (this.sub) this.sub.unsubscribe();
  }
}

```

### 4.4 Interceptor HTTP y Guard

Envía automáticamente el JWT en peticiones al backend y protege las rutas .

**token.interceptor.ts:**

```typescript
import { HttpInterceptorFn } from '@angular/common/http';

export const tokenInterceptor: HttpInterceptorFn = (req, next) => {
  const token = localStorage.getItem('token');
  if (token) {
    const authReq = req.clone({
      setHeaders: { Authorization: `Bearer ${token}` }
    });
    return next(authReq);
  }
  return next(req);
};

```

**auth.guard.ts:**

```typescript
import { CanActivateFn, Router } from '@angular/router';
import { inject } from '@angular/core';
import { AuthService } from './auth.service';

export const authGuard: CanActivateFn = (route, state) => {
  const authService = inject(AuthService);
  const router = inject(Router);

  if (authService.estaAutenticado()) {
    return true;
  }
  router.navigate(['/login'], { queryParams: { returnUrl: state.url } });
  return false;
};

```

---

## 5. Recomendaciones de Seguridad (Checklist IA)

Instruye a tu asistente de IA para que respete estas reglas:

* 
**Nunca confíes en el token decodificado en el cliente:** La validación de firma y los claims se procesan estrictamente en el backend.


* 
**Comprobación del Audience (`aud`):** Asegúrate de que el backend valide que el token fue emitido específicamente para tu Client ID.


* 
**HTTPS:** En producción, fuerza HTTPS y utiliza el atributo `Secure` si optas por manejar sesiones por cookies.