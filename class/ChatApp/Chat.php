<?php
namespace ChatApp;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
 
	 	if ( !isset($GLOBALS['playing']) ){					
			$GLOBALS['playing'] = 0;		
		}

	 	if ( !isset($GLOBALS['line']) ){					
			$GLOBALS['line'] = array();		
		}

	 	if ( !isset($GLOBALS['score'][0]) ){					
			$GLOBALS['score'][0] = 0;		
		}

	 	if ( !isset($GLOBALS['score'][1]) ){					
			$GLOBALS['score'][1] = 0;		
		}
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

		$message = json_decode($msg, true);

		if( isset($message['type']) ){
			if($message['type'] == 'strChat'){
		        foreach ($this->clients as $client) {
		            if ($from == $client) {
		                // The sender is not the receiver, send to each client connected
			            $msg = json_encode(
			                array('type' => 'chatId' , 'text' => $from->resourceId)
			            );
		                $client->send($msg);
		            }
		        }		
			} else if( $message['type'] == 'friendChatId' && ($from->resourceId !== (int) $message['text'] ) ){

				$c = 0;

			    foreach ($this->clients as $client) {
			        if ($message['text'] == $client->resourceId) {
						$c++;
			        }
				}
					
				if ($c > 0){

					if($GLOBALS['playing'] == 0){					
						$GLOBALS['playing'] = 1;
						array_push($GLOBALS['line'], $from->resourceId, $message['text']);
	//					file_get_contents('http://soyfanbot.com/remote.php?name=futy');
					    foreach ($this->clients as $client) {
					        if ($from == $client) {
					            // The sender is not the receiver, send to each client connected
						        $msg = json_encode(
						            array('type' => 'play', 'text' => 'play')
						        );
					            $client->send($msg);
					        } else if ($message['text'] == $client->resourceId) {
					            // The sender is not the receiver, send to each client connected
						        $msg = json_encode(
						            array('type' => 'play', 'text' => 'play')
						        );
					            $client->send($msg);
					        } else {
					            // The sender is not the receiver, send to each client connected
					            $vs = $from->resourceId . ' vs ' . $message['text'];
						        $msg = json_encode(
						            array('type' => 'onMatch', 'text' => $vs ) 
						            );
					            $client->send($msg);
					        }
			
					    }
					} else if ($GLOBALS['playing'] == 1){
					    foreach ($this->clients as $client) {
					        if ($from == $client) {
					            // The sender is not the receiver, send to each client connected
						        $msg = json_encode(
						            array('type' => 'play', 'text' => 'wait')					        );
					            $client->send($msg);
					        } else if ($message['text'] == $client->resourceId) {
					            // The sender is not the receiver, send to each client connected
						        $msg = json_encode(
						            array('type' => 'play', 'text' => 'wait')
						        );
					            $client->send($msg);
					        } else {
					            // The sender is not the receiver, send to each client connected
					            $vs = $from->resourceId . ' vs ' . $message['text'];
						        $msg = json_encode(
						            array('type' => 'onWait', 'text' => $vs ) 
						            );
					            $client->send($msg);
					        }
			
					    }				
					}
	
					
				}
				
			} else if($message['type'] == 'goal'){


					$scorer = (int)$message["text"]-1;
					$GLOBALS['score'][$scorer]++;
				    foreach ($this->clients as $client) {

				        if ($GLOBALS['line'][$scorer] == $client->resourceId) {
				            // The sender is not the receiver, send to each client connected
					        $msg = json_encode(
					            array('type' => 'goal', 'text' => $GLOBALS['score'][$scorer])
					        );
				            $client->send($msg);
				        } else {
				            // The sender is not the receiver, send to each client connected
				            $goal = 'for ' . $message['text'];
					        $msg = json_encode(
					            array('type' => 'point', 'text' => $goal ) 
					            );
				            $client->send($msg);
				        }
		
					if( ($GLOBALS['score'][0] + $GLOBALS['score'][1]) >= 5){

						if ($GLOBALS['score'][0] > $GLOBALS['score'][1]){
							$winner = 0;
							$losser = 1; 
						} else {
							$winner = 0; 							
							$losser = 1; 
						}
						
						$GLOBALS['score'][0] = $GLOBALS['score'][1] = 0; 

					    foreach ($this->clients as $client) {

					        if ($GLOBALS['line'][$winner] == $client->resourceId) {
					            // The sender is not the receiver, send to each client connected
						        $msg = json_encode(
						            array('type' => 'final', 'text' => 'win')
						        );
					            $client->send($msg);
					        } else if ( $GLOBALS['line'][$losser] == $client->resourceId ) {
					            // The sender is not the receiver, send to each client connected
						        $msg = json_encode(
						            array('type' => 'final', 'text' => 'lose')
						        );
					            $client->send($msg);
						        
					        }else {
					            // The sender is not the receiver, send to each client connected
					            $goal = 'Winner ' . ($winner+1);
						        $msg = json_encode(
						            array('type' => 'final', 'text' => $goal ) 
						            );
					            $client->send($msg);
					        }
					    }	
						
					}		


				    }			
			}  else {
		        foreach ($this->clients as $client) {
		            if ($from !== $client) {
		                // The sender is not the receiver, send to each client connected
		                $client->send($msg);
		            }
		        }
		    }
		}
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }

	public function send($client, $msg){
	        $this->say("> ".$msg);
	        $messageRequest = json_decode($msg,true);
	
	            // $action=$messageRequest[0];
	            $action = 'responseMessage';
	            $param  = $messageRequest[1]['data'];
	        if( method_exists('socketWebSocketTrigger',$action) ){
	                                $response = socketWebSocketTrigger::$action($param);
	                            }
	            $msg = json_encode(
	                array(                      
	                'message',
	                    array('data' => $response)
	                )
	            );
	
	            $msg = $this->wrap($msg);
	
	        socket_write($client, $msg, strlen($msg));
	    }

}
