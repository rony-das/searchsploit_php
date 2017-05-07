<?php

function banner(){
    return "
   _____                     _      _____       _       _ _   
  / ____|                   | |    / ____|     | |     (_) |  
 | (___   ___  __ _ _ __ ___| |__ | (___  _ __ | | ___  _| |_ 
  \___ \ / _ \/ _` | '__/ __| '_ \ \___ \| '_ \| |/ _ \| | __|
  ____) |  __/ (_| | | | (__| | | |____) | |_) | | (_) | | |_ 
 |_____/ \___|\__,_|_|  \___|_| |_|_____/| .__/|_|\___/|_|\__|
                                         | |                  
                                         |_|      - PHP Version!            
        Credits: Offensive Security
        Coded by - Pwnstar(twitter.com/0xrony)
        Taking NO credits, just ported it to PHP.

";
}

function software_update(){

    // this function is still not added (because of laziness)

    $offsec_main_repo = "https://github.com/offensive-security/exploit-database/archive/master.zip";

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $offsec_main_repo);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSLVERSION,3);
    $repo_file = curl_exec($curl);
    curl_close($curl);
    $write_repo = fopen("offsec.zip", "w+");
                  fputs($write_repo, $repo_file);
                  fclose($write_repo);

    // bash please!

    system("sudo unzip offsec.zip");
    system("sudo rm -rf db/");
    system("sudo mkdir db/");
    system("mv files.csv db/");

}


// some cleanings..

system("wget https://github.com/offensive-security/exploit-database/archive/master.zip -O temp_offsec.zip");
system("sudo unzip temp_offsec.zip");
system("cd exploit-database-master/;cp -r * ..;");
system("sudo rm -rf exploit-database-master/");
system("sudo mkdir db/; sudo chmod 777 db/");
system("sudo mv files.csv db/");
system("sudo rm -rf README.md");
system("sudo rm -rf searchsploit");
system("sudo rm -rf temp_offsec.zip");


// change this to your own database details as well as host.
check_database("root", "root", "localhost");


function check_database($username, $password, $hostname){

        /** No one wants dependencies **/

        $connect = mysqli_connect($hostname, $username, $password);

        if(mysqli_connect_errno($connect)){

            die("Connection failed..");

        }

        if(!$connect->select_db("myexploit")){

            echo "[*] A database will be created with the name: 'myexploit' do you want to continue? Y/N- ";
            $handle = fopen("php://stdin", "r");
            $get_answer_db = trim(fgets($handle));
            fclose($handle);

            switch (strtolower($get_answer_db)){

                case "y":
                    $query = "CREATE DATABASE `myexploit`;";

                    if ($connect->query($query) == true){
                            sleep(1);
                            echo "[*] Database created successfully with the name 'myexploit'\n";
                            if ($connect->select_db("myexploit")){

                                $create_table_query = "CREATE TABLE exploits(`id`   INT NOT NULL, `file` VARCHAR(255) NOT NULL,`description` VARCHAR(255) NOT NULL, `date`  DATE NOT NULL,`author` VARCHAR(255) NOT NULL,`platform` VARCHAR(255) NOT NULL,`type` VARCHAR(255) NOT NULL,`port` INT NOT NULL, PRIMARY KEY(id));";
                                if ($connect->query($create_table_query) == true){
                                    sleep(1);
                                    echo "[*] Tables inside the database placed successfully..\n";
                                    echo "[*] Importing..\n";
                                    sleep(1);
                                    echo "[*] Only the first time it imports takes time, unless you update (update feature not yet appended) and keep patience..\n";
                                    echo "NOTE: Approximately wait for 30-35 minutes to get imported..\n";


                                    if (($handle = fopen("db/files.csv", "r")) !== FALSE) {
                                        fgetcsv($handle);
                                        while (($data = fgetcsv($handle, ",")) !== FALSE) {
                                            // stolen this part from php dev zone
                                            $num = count($data);

                                            for ($c = 0; $c < $num; $c++) {

                                                $col[$c] = $data[$c];

                                            }

                                            $id              = $col[0];
                                            $file            = $col[1];
                                            $description     = $col[2];
                                            $date            = $col[3];
                                            $author          = $col[4];
                                            $platform        = $col[5];
                                            $type            = $col[6];
                                            $port            = $col[7];


                                            $fill_in_query = "INSERT INTO exploits(`id`,`file`,`description`,`date`,`author`,`platform`,`type`,`port`) VALUES($id ,'$file','".mysqli_real_escape_string($connect,$description)."','$date','".mysqli_real_escape_string($connect,$author)."','$platform','$type',$port);";

                                            if($connect->query($fill_in_query) == true){

                                                //

                                            }else{

                                                die("[-] Something went wrong while importing the database..\n");
                                            }



                                        }


                                    }else{
                                        echo "[-] CSV file not readable check the permissions!";
                                    }


                                }else{
                                    die("[-] An Error is generated while creating the tables..\n");
                                }

                            }

                    }

                    break;
                case "n":
                    die("[-] Either you create database manually or via this script, a database is must for this script!\n");

            }


        }else{
            //The banner will go here..
            echo banner();

           $rep_do  = fopen("php://stdin","r");
           while(@$get_rep != 'exit'){

               echo "\n>> ";
               $get_rep = trim(fgets($rep_do));

               if($get_rep == '' || $get_rep == 'exit'){
                   die("Bye!\n");
               }

               $search_query = "SELECT id,description,file,date FROM exploits WHERE description LIKE '%".mysqli_real_escape_string($connect,$get_rep)."%';";

               $search_result = $connect->query($search_query);

               if ($search_result->num_rows > 0){

                   while($row = $search_result->fetch_assoc()){

                       echo "\n".$row["description"]."\n"."Link =>  https://www.exploit-db.com/exploits/".$row["id"]."\nDate: ".$row["date"]."\n"."Exploit File: ".$row["file"]."\n\n";

                   }

               }


           }
        }

}
