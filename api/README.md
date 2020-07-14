# Setup Unix (on ssh servers)
 - `sudo apt install zip`

# Setup Env

-   [Env Documentation](ENV.md)

# Setup Apache

-   [Apache Documentation](APACHE.md)

# Setup Websocket

-   [WebSocket Documentation](WEBSOCKET.md)

# Help

-   [Help Documentation](HELP.md)

# Navigation

-   [User documentation](#user)
-   [Server documentation](#server)
-   [Token documentation](#token)
-   [Document documentation](#document)
-   [SSH documentation](#ssh)

# **User**

## Navigation

-   [Get User By ID](#get-user-by-id)
-   [Get User By EMAIL](#get-user-by-email)
-   [Post new User](#post-new-user)
-   [Get all Users](#get-all-users)
-   [Delete User By ID](#delete-user-by-id)
-   [Update User By ID](#update-user-by-id)

## **Get User by ID**

-   ### **Request**
          Route : /user/{id}
          Method : GET
          Url Params:
              {id} - Integer - The user identifier
-   ### **Response**
    -   ### If {ID} parameter is not a number :
        ```json
        {
            "status": 404,
            "data": "{ID} parameter is not a number."
        }
        ```
    -   ### If is the same user or is an Administrator :
        ```json
        {
            "status": 404,
            "data": "You don't have the authorization."
        }
        ```
    -   ### If user is not found with {ID} :
        ```json
        {
            "status": 404,
            "data": "user not found."
        }
        ```
    -   ### If user was found with {ID} :
        ```json
        {
            "status": 200,
            "data": {
                "id": "",
                "firstname": "",
                "lastname": "",
                "email": "",
                "verified": "",
                "roles": []
            }
        }
        ```

## **Get User by EMAIL**

-   ### **Request**
          Route : /user/email/{email}
          Method : GET
          Url Params:
              {email} - String - The user email
-   ### **Response**
    -   ### If {EMAIL} parameter is not a email :
        ```json
        {
            "status": 404,
            "data": "{EMAIL} parameter is not a email."
        }
        ```
    -   ### If user not found :
        ```json
        {
            "status": 404,
            "data": "user not found."
        }
        ```
    -   ### If user was found with {EMAIL} :
        ```json
        {
            "status": 200,
            "data": {
                "id": "",
                "firstname": "",
                "lastname": "",
                "email": "",
                "verified": true || false,
                "roles": []
            }
        }
        ```

## **Post New User**

-   ### **Request**
          Route : /user
          Method : POST
          Body Params:
              {firstname} - String
              {lastname}  - String
              {email}     - String
              {password}  - String
-   ### **Response**
    -   ### If the parameters is not found :
        ```json
        {
            "status": 404,
            "data": "The parameters cannot be found."
        }
        ```
    -   ### If the user account is not created :
        ```json
        {
            "status": 404,
            "data": "Account not created."
        }
        ```
    -   ### If the user account is created :
        ```json
        {
            "status": 200,
            "data": "Account created."
        }
        ```

## **Get All Users**

-   ### **Request**
          Route : /users
          Method : GET
-   ### **Response**
    -   ### If is not a Administrator :
        ```json
        {
            "status": 404,
            "data": "You don't have the authorization."
        }
        ```
    -   ### If users not found :
        ```json
        {
            "status": 404,
            "data": "Users not found."
        }
        ```
    -   ### If users found :
        ```json
        {
            "status": 200,
            "data": [
                {
                    "id": "",
                    "firstname": "",
                    "lastname": "",
                    "email": "",
                    "verified": true || false,
                    "roles": []
                }
            ]
        }
        ```

## **Delete User By ID**

-   ### **Request**
          Route : /user/{id}
          Method : DELETE
          Url Params:
              {id} - Integer - The user identifier
-   ### **Response**
    -   ### If {ID} parameter is not a number :
        ```json
        {
            "status": 404,
            "data": "{ID} parameter is not a number."
        }
        ```
    -   ### If is not the same user and is not an Administrator :
        ```json
        {
            "status": 404,
            "data": "You don't have the authorization."
        }
        ```
    -   ### If user is not found with {ID} :
        ```json
        {
            "status": 404,
            "data": "user not found."
        }
        ```
    -   ### If user was deleted with {ID} :
        ```json
        {
            "status": 200,
            "data": "User deleted"
        }
        ```
    -   ### If user was not deleted with {ID} :
        ```json
        {
            "status": 404,
            "data": "User not deleted"
        }
        ```

## **Update User By ID**

-   ### **Request**
          Route : /user/update/{id}
          Method : POST
          Url Params:
              {id} - Integer - The user identifier
          Body Params (optional) :
              {firstname} - String
              {lastname}  - String
              {email}     - String
              {password}  - String
-   ### **Response**
    -   ### If {ID} parameter is not a number :
        ```json
        {
            "status": 404,
            "data": "{ID} parameter is not a number."
        }
        ```
    -   ### If parameters not found :
        ```json
        {
            "status": 404,
            "data": "Parameters not found."
        }
        ```
    -   ### If is the same user or is an Administrator :
        ```json
        {
            "status": 404,
            "data": "You don't have the authorization."
        }
        ```
    -   ### If user is not found with {ID} :
        ```json
        {
            "status": 404,
            "data": "user not found."
        }
        ```
    -   ### If user was updated with {ID} :
        ```json
        {
            "status": 200,
            "data": ["Password updated.", "firstname updated.", "lastname updated.", "email updated."]
        }
        ```
    -   ### If user was not updated with {ID} :
        ```json
        {
            "status": 200,
            "data": ["Password not updated.", "firstname not updated.", "lastname not updated.", "email not updated."]
        }
        ```

---

# **Server**

## Navigation

-   [Get Server By ID](#get-server-by-id)
-   [Get All Servers](#get-all-servers)
-   [Get All Server Documents](#get-all-server-documents)
-   [Update Server By ID](#update-server-by-id)
-   [Create New Server](#create-new-server)
-   [Delete Server By ID](#delete-server-by-id)

## **Get Server By ID**

-   ### **Request**
          Route : /server/{id}
          Method : GET
          Url Params:
              {id} - Integer - The server identifier

## **Get All Servers**

-   ### **Request**
          Route : /servers
          Method : GET

## **Get All Server Documents**

-   ### **Request**
          Route : /server/{id}/documents
          Method : GET
          Url Params:
              {id} - Integer - The server identifier

## **Update Server By ID**

-   ### **Request**
          Route : /server/{id}/update
          Method : POST
          Url Params:
              {id} - Integer - The server identifier
          Body Params (optional) :
              {name}          - String
              {adresse}       - String
              {port}          - String
              {username}      - String
              {password}      - String
              {privateKey}    - String  (Optional)

## **Create New Server**

-   ### **Request**
          Route : /server
          Method : POST
          Url Params:
              {id} - Integer - The server identifier
          Body Params (optional) :
              {name}          - String
              {adresse}       - String
              {port}          - String
              {username}      - String
              {password}      - String
              {privateKey}    - String  (Optional)

## **Delete Server By ID**

-   ### **Request**
          Route : /server/{id}
          Method : DELETE
          Url Params:
              {id} - Integer - The server identifier

---

# **Token**

## Navigation

-   [Get Token By ID](#get-token-by-id)
-   [Get Token By Token](#get-token-by-token)
-   [Create New Token](#create-new-token)
-   [Delete Token By ID](#delete-token-by-id)

## **Get Token By ID**

-   ### **Request**
          Route : /token/{id}
          Method : GET
          Url Params:
              {id} - Integer - The token identifier

## **Get Token By Token**

-   ### **Request**
          Route : /token/generated/{token}
          Method : GET
          Url Params:
              {token} - String - The token

## **Create New Token**

-   ### **Request**
          Route : /token
          Method : POST
          * Require User Auth

## **Delete Token By ID**

-   ### **Request**
          Route : /token/{id}
          Method : DELETE
          Url Params:
              {id} - String - The token identifier

---

# **Document**

## Navigation

-   [Get Document By ID](#get-document-by-id)
-   [Search Document](#search-document)
-   [Create New Document](#create-new-document)
-   [Delete Document By ID](#delete-document-by-id)

## **Get Document By ID**

-   ### **Request**
          Route : /document/{id}
          Method : GET
          Url Params:
              {id} - Integer - The document identifier

## **Search Document**

-   ### **Request**
          Route : /document/search/q
          Method : POST
          Body Params :
              {name}      - String
              {location}  - String
              {server_id} - Integer

## **Create New Document**

-   ### **Request**
          Route : /document
          Method : POST
          Body Params :
              {name}      - String
              {size}      - String|(Inetger|Float)
              {user_id}   - Integer
              {location}  - String
              {server_id} - Integer

## **Delete Document By ID**

-   ### **Request**
          Route : /document/{id}
          Method : DELETE
          Url Params:
              {id} - Integer - The document identifier

---

# **SSH**

## Navigation

-   [Execute Unix Command in Server](#execute-unix-command-in-server)

## **Execute Unix Command in Server**

-   ### **Request**
          Route : /ssh/exec
          Method : POST
          Body Params :
              {host}      - String
              {port}      - Integer
              {login}     - String
              {command}   - String (exemple: command = pwd)
