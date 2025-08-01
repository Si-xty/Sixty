<?php
        class UserModel extends CI_Model 
        {
                // function __construct(){
                //         parent::__construct();
                //         $this->load->database();
                // }
                
                public function loginUser($email)
                {
                        $this->db->select('*');
                        $this->db->group_start();
                        $this->db->where('email', $email);
                        $this->db->or_where('user', $email);
                        $this->db->group_end();
                        $this->db->limit(1);
                        $query = $this->db->get('users');
                        if($query->num_rows() === 1){
                                return $query->row();
                        }else{
                                return false;
                        }
                }

                public function mostrarUsuariosByUser($user)
                {
                        $this->db->select('*');
                        $this->db->where('user', $user);
                        $this->db->from('users');
                        $this->db->limit(1);
                        $query = $this->db->get();
                        if($query->num_rows() == 1){
                                return $query->row();
                        }else{
                                return false;
                        }
                }

                public function mostrarUsuarios()
                {
                        $this->db->select('rut, first_name, last_name, email, user, rol, phone_num, location, active, last_conn, "X" as acciones');
                        $this->db->from('users');
                        $query = $this->db->get();
                        if($query->num_rows() > 0){
                                $usuarios = $query->result_array();
                                foreach ($usuarios as &$usuario) {
                                if ($usuario['location'] == '1') {
                                        $usuario['location'] = 'Talca';
                                } elseif ($usuario['location'] == '2') {
                                        $usuario['location'] = 'Rancagua';
                                }
                                }
                                foreach ($usuarios as &$usuario) {
                                        if ($usuario['rol'] == '3') {
                                                $usuario['rol'] = 'Administrador';
                                        } elseif ($usuario['rol'] == '2') {
                                                $usuario['rol'] = 'Trabajador';
                                        } elseif ($usuario['rol'] == '1') {
                                                $usuario['rol'] = 'Solicitante'; 
                                        }
                                        }

                                return $usuarios;
                        }else{
                                return [];
                        }
                }

                public function registerUser($data) {
                        // Calcular el dígito verificador del RUT
                        $data['rut'] = $data['rut'] . '-' . $this->calcularDV($data['rut']);
                        
                        return $this->db->insert('users', $data);
                    }

                // public function updateUser($data)
                // {
                //         $dataToUpdate = [
                //                 'rut' => $data['rut'],
                //                 'first_name' => $data['first_name'],
                //                 'last_name' => $data['last_name'],
                //                 'email' => $data['email'],
                //                 'password' => $data['password'],
                //                 'user' => $data['user'],
                //                 'rol' => $data['rol'],
                //                 'ubicacion' => $data['ubicacion'],
                //                 'activo' => $data['activo'],
                //                 'phone_num' => $data['phone_num'],
                //                 'ultima_conexion' => $data['ultima_conexion'],
                //                 //ubicacion
                //                 //rol
                //         ];

                //         $result = $this->loginUser($data);

                //         $this->db->where("(id = '{$result['id']}'");
                //         $this->db->update('users', $dataToUpdate);

                //         if ($this->db->affected_rows() > 0) 
                //         {
                //                 return true; // Actualización exitosa
                //         } 
                //         else 
                //         {
                //                 return false; // No se actualizó ningún registro
                //         }
                // }

                public function updateDataUser($userId, $data)
                {
                // Realizar la actualización en la tabla 'users' utilizando el ID del usuario
                $this->db->where('rut', $userId);
                if ($this->db->update('users', $data)) {
                        return true;
                } else {
                        return false;
                }
                }

                public function updateConnection($user)
                {
                $data = $this->mostrarUsuariosByUser($user);
                if ($data) {
                        $this->db->set('last_conn', 'NOW()', FALSE);
                        $this->db->where('user', $user);
                        return $this->db->update('users');
                }
                return false;
                }

                // public function deleteUser($data)
                // {

                // }

                function calcularDV($rut) {
                        $suma = 0;
                        $multiplicador = 2;
                    
                        for ($i = strlen($rut) - 1; $i >= 0; $i--) {
                            $suma += $rut[$i] * $multiplicador;
                            $multiplicador = ($multiplicador == 7) ? 2 : $multiplicador + 1;
                        }
                    
                        $dv = 11 - ($suma % 11);
                        if ($dv == 11) return '0';
                        if ($dv == 10) return 'K';
                        return (string)$dv;
                }

                
                // --- Métodos para Google Login ---
                public function getUserByGoogleId($google_id)
                {
                        $this->db->where('google_id', $google_id);
                        $query = $this->db->get('users');
                        return $query->row();
                }

                public function getUserByEmail($email)
                {
                        $this->db->where('email', $email);
                        $query = $this->db->get('users');
                        return $query->row();
                }

                public function registerGoogleUser($data) {
                        
                        $data_to_insert = [
                        'google_id' => $data['google_id'],
                        'email' => $data['email'],
                        'user' => $data['user'],
                        'first_name' => $data['first_name'] ?? '',
                        'last_name' => $data['last_name'] ?? '',
                        'full_name' => $data['full_name'] ?? '',
                        'rol' => $data['rol'] ?? 'usuario',
                        'password' => $data['password'] ?? null,
                        'picture' => $data['picture'] ?? '',
                        'rut' => $data['rut'] ?? null,
                        'phone_num' => $data['phone_num'] ?? null,
                        'location' => $data['location'] ?? null,
                        'active' => $data['active'] ?? 'si',
                        ];
                        
                        $this->db->insert('users', $data_to_insert);
                        return $this->db->insert_id();
                }

                public function updateUser($id, $data)
                {
                        $this->db->where('id', $id);
                        $this->db->update('users', $data);
                        return $this->db->affected_rows();
                }

                public function getUserById($id)
                {
                        $this->db->where('id', $id);
                        $query = $this->db->get('users');
                        return $query->row();
                }
 
        }
?>