<?php
/**
 * Copyright 2014 Krzysztof Magosa
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace Site\Controller;

use KM\Saffron\RoutingResult;

class ErrorController
{
    public function notFoundAction(RoutingResult $result)
    {
        header($_SERVER["SERVER_PROTOCOL"] .' 404 Not Found');
        echo 'Error 404';
    }

    public function methodNotAllowedAction(RoutingResult $result)
    {
        header($_SERVER["SERVER_PROTOCOL"] .' 405 Method Not Allowed');
        // This header is required by RFC 2616
        header('Allow: ' . implode(', ', $result->getAllowedMethods()));

        echo 'Error 405<br>';
        echo 'Try with one of them: ' . implode(', ', $result->getAllowedMethods());
    }
}
