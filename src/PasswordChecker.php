<?php
    namespace unique\waspasswordpwned;

    class PasswordChecker {

        const URL_API = 'https://api.pwnedpasswords.com/range/';

        protected \GuzzleHttp\Client $client;

        protected bool $ignore_exceptions = true;

        protected ?\Exception $last_exception = null;

        protected static ?self $instance = null;

        /**
         * PasswordChecker constructor.
         * $options can have this options:
         *  - (bool) 'ignore_exceptions' = true: If an exception happens during a check, save it and ignore it.
         *                                       It can later be retrived with {@see getLastException()}
         *
         * @param array $options
         * @param array $client_options
         */
        public function __construct( array $options = [], array $client_options = [] ) {

            if ( isset( $options['ignore_exceptions'] ) ) {

                $this->ignore_exceptions = $options['ignore_exceptions'];
            }

            $this->client = new \GuzzleHttp\Client( $client_options );
        }

        /**
         * Returns a static instance of the tool.
         * @return PasswordChecker
         */
        public static function getInstance() {

            if ( self::$instance === null ) {

                self::$instance = new self();
            }

            return self::$instance;
        }

        /**
         * Checks password's hash agains haveibeenpwned.com database.
         * Will return a number of hits or null in case of an error.
         * If an Exception is thrown it is saved and can be retrieved via {@see getLastException()} method.
         * If an option ignore_exceptions = false was passed via constructor, it will be also thrown.
         *
         * @param string $password_sha1 - Password hashed in sha1.
         * @return int|null
         * @throws \GuzzleHttp\Exception\GuzzleException
         */
        public function checkIfPasswordPwned( string $password_sha1 ): ?int {

            if ( strlen( $password_sha1 ) < 5 ) {

                return null;
            }

            try {

                $prefix = substr( $password_sha1, 0, 5 );
                $suffix = strtoupper( substr( $password_sha1, 5 ) );
                $response = $this->client->request( 'GET', self::URL_API . $prefix );
                $hashes = explode( "\r\n", (string) $response->getBody() );
                foreach ( $hashes as $hash ) {

                    if ( strpos( $hash, $suffix ) === 0 ) {

                        $data = explode( ':', $hash );
                        return $data[1];
                    }
                }
            } catch ( \Exception $exception ) {

                $this->last_exception = $exception;
                if ( !$this->ignore_exceptions ) {

                    throw $exception;
                }

                return null;
            }

            return 0;
        }

        /**
         * Returns the last exception, or null if non exists.
         * @return \Exception|null
         */
        public function getLastException(): ?\Exception {

            return $this->last_exception;
        }
    }