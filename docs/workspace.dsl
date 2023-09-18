workspace "Ingenium" {

    model {

        user = person "User" "A user with an account and proper permissions"

        thermianCorp = enterprise "Thermian Corporation" {

            admin = person "Administrator" "A corporate member with an account and administration permissions" "Staff"
            member = person "Member" "A corporate member with an account and proper permissions" "Staff"

            thermianWeb = softwareSystem "Thermian Web" "A web interface that allows users to execute AI system functionality" {

                webAuthentication = container "Web Authentication" "A web interface that executes authentication functionality" "PHP, CakePHP,\nHTML, CSS, JS" {

                    webAuthenticationController = group "Controller" {
                        createAccountController = component "Create Account\nController" "Handles HTTP requests to create user accounts"
                        updateAccountController = component "Update Account\nController" "Handles HTTP requests to update user accounts"
                        deleteAccountController = component "Delete Account\nController" "Handles HTTP requests to delete user accounts"
                        loginController = component "Login\nController" "Handles HTTP requests to log in"
                        logoutController = component "Logout\nController" "Handles HTTP requests to log out"
                    }

                    webAuthenticationTemplates = group "Templates" {
                        createAccountTemplates = component "Create Account\nTemplates" "Visual components required to create user accounts"
                        updateAccountTemplates = component "Update Account\nTemplates" "Visual components required to update user accounts"
                        deleteAccountTemplates = component "Delete Account\nTemplates" "Visual components required to delete user accounts"
                        loginTemplates = component "Login\nTemplates" "Visual components required to log in"
                        logoutTemplates = component "Logout\nTemplates" "Visual components required to log out"
                    }

                    webAuthenticationService = group "Authentication Service" {
                        webAuthenticationServiceProvider = component "Web Authentication Service Provider" "Provides the configured authentication service required by the authentication middleware"
                        webAuthenticationCallback = component "Authentication Callback" "Authenticates a user given its credentials. Called by the middleware every time a user logs in"
                    }

                    user -> this "Authenticates using" "" userAuthenticatesUsingWeb
                    admin -> this "Authenticates using" "" adminAuthenticatesUsingWeb
                }

                webAuthorization = container "Web Authorization" "A web interface that executes authorization functionality" "PHP, CakePHP,\nHTML, CSS, JS" {

                    webAuthorizationMiddleware = component "Authorization\nMiddleware" "Ensures the user has authorization to execute a given use case"

                    webAuthorizationControllers = group "Controller" {
                        createRoleController = component "Create Role\nController" "Handles HTTP requests to create a role and associate a set of permissions"
                        showRoleController = component "Show Role\nController" "Handles HTTP requests to display a role and the associated set of permissions"
                        listRolesController = component "List Roles\nController" "Handles HTTP requests to display all available roles and the associated permissions"
                        updateRoleController = component "Update Role\nController" "Handles HTTP requests to update a role and the set of associated permissions"
                        deleteRoleController = component "Delete Role\nController" "Handles HTTP requests to delete a role"
                        listUserPermissionsController = component "List User Permissions\nController" "Handles HTTP requests to display the set of permissions available for a given user"
                        updateUserRoleController = component "Update User Role\nController" "Handles HTTP requests to update the role of a given user"
                    }

                    webAuthorizationTemplates = group "Templates" {
                        createRoleTemplates = component "Create Role\nTemplates" "Visual components required to create a role"
                        showRoleTemplates = component "Show Role\nTemplates" "Visual components required to display a role"
                        listRolesTemplates = component "List Roles\nTemplates" "Visual components required to list available roles"
                        updateRoleTemplates = component "Update Role\nTemplates" "Visual components required to update a role"
                        deleteRoleTemplates = component "Delete Role\nTemplates" "Visual components required to delete a role"
                        listUserPermissionsTemplates = component "List User Permissions\nTemplates" "Visual components required to list user permissions"
                        updateUserRoleTemplates = component "Update User Role\nTemplates" "Visual components required to update a user role"
                    }

                    admin -> this "Manages user permission using"
                }

                hotspotWeb = container "Hotspot Web" "A web interface that executes Hotspot System functionality" "PHP, CakePHP,\nHTML, CSS, JS" {

                    hotspotWebController = group "Controller" {
                        hotspotAnalysisController = component "Hotspot Analysis\nController" "Handles HTTP requests that execute hotspot dataset functionality"
                        hotspotDatasetsController = component "Hotspot Dataset\nController" "Handles HTTP requests that execute hotspot analysis functionality"
                        hotspotResultsController = component "Hotspot\nResults\nController" "Handles HTTP requests that execute hotspot result functionality"
                    }

                    hotspotWebTemplates = group "Templates" {
                        hotspotDatasetsTemplates = component "Hotspot Dataset\nTemplates" "Visual components involved in the execution of hotspot dataset functionality"
                        hotspotAnalysisTemplates = component "Hotspot Analysis\nTemplates" "Visual components involved in the execution of hotspot analysis functionality"
                        hotspotResultsTemplates = component "Hotspot Results\nTemplates" "Visual components involved in the execution of hotspot results functionality"
                    }

                    user -> this "Accesses the Hotspot System using" "" userAccessesHotspotSystem
                }

                xWeb = container "System X Web" "A web interface that executes X functionality" "PHP,  CakePHP,\nHTML, CSS, JS" {
                    user -> this "Accesses the System X using" "" userAccessesXSystem
                }

                user -> this "Accesses AI systems using"
                member -> this "Accesses AI systems using"
                admin -> this "Manages user permissions using"

            }

            thermianApp = group "Thermian App" {

                accessSystem = softwareSystem "Access System" "Provides authorization and authentication functionality" {

                    authorization = container "Authorization" "Creates roles, associates a set of permissions with a given role and assigns roles to users" "PHP" {
                        webAuthorization -> this "Interfaces"

                        authorizationUseCases = group "Use Cases" {
                            listPermissions = component "List Permissions" "Returns the set of available permissions"
                            createRole = component "Create Role" "Creates a role and associates some permissions"
                            listRolePermissions = component "List Role Permissions" "Returns the set of permissions associated with a role"
                            updateRolePermissions = component "Update Role Permissions" "Updates the set of permissions associated with a role"
                            listRoles = component "List Roles" "Returns the set of available roles"
                            deleteRole = component "Delete Role" "Deletes a role that is no longer in use"
                            checkUserPermission = component "Check User Permission" "Checks whether a user has a given permission"
                            findUserRole = component "Find User Role" "Returns the role of a given user"
                            updateUserRole = component "Update User Role" "Updates the name and the set of permissions associated with a role"
                            listUsersWithRole = component "List Users With Role" "Returns the set of users who have a given permission"
                        }

                        authorizationPersistence = group "Persistence" {
                            mySqlUserRolesRepository = component "MySQL User Roles Repository" "MySQL implementation of the User Role Repository"
                            mySqlRoleRepository = component "MySQL Role Repository" "MySQL implementation of the Role Repository"
                            mySqlRolePermissionRepository = component "MySQL Role Permissions Repository" "MySQL implementation of the Role Permission Repository"
                            mySqlPermissionRepository = component "MySQL Permission Repository" "MySQL implementation of the Permission Repository"
                        }

                        authorizationDomain = group "Domain" {
                            roles = component "Role" "Grants a set of permissions to a user"
                            permissions = component "Permission" "Allows roles to execute a given use case"
                            userRolesRepository = component "User Roles Repository" "Stores and retrieves user - role associations"
                            roleRepository = component "Role Repository" "Stores and retrieves roles"
                            rolePermissionRepository = component "Role Permissions Repository" "Stores and retrieves role - permission associations"
                            permissionRepository = component "Permission Repository" "Stores and retrieves permissions"
                        }

                    }

                    authorizationDatabase = container "Authorization Database" "Stores roles, permissions, user-role associations and role-permission associations" "MySQL Database" "Database" {
                        authorization -> this "Reads from and writes to" "PDO"
                    }

                    authentication = container "Authentication" "Creates and manages user accounts and provides login and logout functionality" "PHP" {

                        authenticationUseCases = group "Use Cases" {
                            createUser = component "Create User" "Registers a user in the system"
                            updateUser = component "Update User" "Updates user information and credentials"
                            checkUserExists = component "Check User Existence" "Asserts the existence of a user with a given identifier"
                            authenticateUSer = component "Authenticate User" "Asserts the existence of a user with the given credentials"
                            deleteUser = component "Delete User" "Deletes a user from the system"
                        }

                        authenticationDomain = group "Domain" {
                            userComponent = component "User" "Contains account information and credentials"
                            userRepository = component "User Repository" "Stores user account information"
                        }

                        persistenceDomain = group "Persistence" {
                            mySqlUserRepository = component "MySQL User Repository" "MySQL implementation of User Repository"
                        }

                        authorization -> this "References users from"
                        webAuthentication -> this "Interfaces"
                    }

                    usersDatabase = container "Users Database" "Stores user accounts and credentials" "MySQL Database" "Database" {
                        authentication -> this "Reads from and writes to" "PDO"
                    }

                }

                hotspotSystem = softwareSystem "Hotspot System" "Provides hotspot detection and analysis functionality" {

                    hotspotApplication = container "Hotspot Application" "" "PHP" {

                        hotspotAppAnalysis = component "Hotspot Analysis" "Structures hotspot detection results and provides analysis functionality"

                        hotspotAppResult = component "Hotspot Results" "Creates manages and deletes hotspot detection results" {
                            hotspotAppAnalysis -> this "reads hotspot detection results using"
                        }

                        hotspotAppDetection = component "Hotspot Detection" "Provides an interface to execute hotspot detection functionality" {
                            hotspotAppAnalysis -> this "Executes hotspot AI functionality using"
                        }

                        hotspotAppDataset = component "Hotspot Dataset" "Creates, manages and deletes hotspot input datasets" {
                            hotspotAppAnalysis -> this "reads hotspot datasets using"
                        }

                        hotspotWeb -> this "Interfaces"
                    }

                    hotspotAnalysisDatabase = container "Hotspot Analysis" "Stores structured hotspot analysis results" "MySQL Database" "Database" {
                        hotspotAppAnalysis -> this "Stores structured analysis results into" "PDO"
                    }

                    hotspotAISystem = container "Hotspot AI System" "" "Python" {
                        hotspotAIDetection = component "Hotspot Detection" "Hosts the AI that provides hotspot detection functionality"
                        hotspotAIResults = component "Hotspot Results" "Stores hotspot detection results" {
                            hotspotAIDetection -> this "stores detection results using"
                        }
                        hotspotAIDataset = component "Hotspot Dataset" "Retrieves solar panel input" {
                            hotspotAIDetection -> this "reads hotspot datasets using"
                        }
                        hotspotAppDetection -> this "Executes hotspot detections using" "Command Line"
                    }

                    hotspotDatasetDatabase = container "Hotspot Datasets" "Stores datasets of thermographic solar panel images" "MySQL Database" "Database" {
                        hotspotAppDataset -> this "Stores solar panel datasets into" "PDO"
                        hotspotAIDataset -> this "Reads solar panel images from" "MySQL Connector"
                    }

                    hotspotDetectionDatabase = container "Hotspot Detection" "Stores unstructured hotspot detection results in csv and image formats" "MySQL Database" "Database" {
                        hotspotAppResult -> this "Reads hotspot detection result from" "PDO"
                        hotspotAIResults -> this "Stores hotspot detection result into" "MySQL Connector"
                    }

                }

                aiSystemX = softwareSystem "AI System X" "Provides X functionality" {
                    xWeb -> this "Interfaces"
                }

            }

        }

    }

    views {

        systemLandscape thermianLandscape "Thermian Corporation Landscape" {
            include user admin member thermianWeb thermianApp
            exclude relationship.tag==userAuthenticatesUsingWeb
            exclude relationship.tag==adminAuthenticatesUsingWeb
        }

        container thermianWeb {
            include * thermianApp
        }

        component webAuthentication {
            include * accessSystem
        }

        component webAuthorization {
            include * accessSystem
        }

        component hotspotWeb {
            include * hotspotSystem
        }

        container accessSystem {
            include *
            exclude thermianWeb
            include ->webAuthentication ->webAuthorization
        }

        component authorization {
            include * webAuthorization authorizationDatabase authentication
        }

        component authentication {
            include * webAuthentication usersDatabase
        }

        container hotspotSystem {
            include *
            exclude thermianWeb
            include ->hotspotWeb
        }

        component hotspotApplication {
            include * hotspotAISystem hotspotAnalysisDatabase hotspotDatasetDatabase hotspotDetectionDatabase
            include hotspotWeb
        }

        component hotspotAISystem {
            include * hotspotApplication hotspotDatasetDatabase hotspotDetectionDatabase
            exclude hotspotApplication->*
        }

        styles {
            element "Element" {
                shape RoundedBox
            }
            element "Software System" {
                background #1168bd
                color #ffffff
            }
            element "Container" {
                background #438dd5
                color #ffffff
            }
            element "Component" {
                background  #85bbf0
                color #000000
            }
            element "Person" {
                background #08427b
                color #ffffff
                shape Person
            }
            element "Staff" {
                background #7287a6
                color #ffffff
                shape Person
            }
            element "Infrastructure Node" {
                background #ffffff
            }
            element "Database" {
                shape Cylinder
                background #6d879c
            }
        }
    }

}