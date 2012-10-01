<?php
    $proc = array(
        "dump" => "SELECT getdate(), * FROM INFORMATION_SCHEMA.Routines",
        "diff" => "SELECT getdate(), * FROM INFORMATION_SCHEMA.Routines where last_altered >= DATEADD(dd, 0, DATEDIFF(dd, 0, GETDATE()))"
    );

    error_reporting(E_ALL);

    ini_set('display_errors', true);

    $config_file = "settings.ini";
    $config = parse_ini_file( $config_file );

    if( array_key_exists("backup_location", $config ) === false )
    {
        $backup_location = dirname(__FILE__);
    }
    else
    {
        $backup_location = $config["backup_location"];
    }

    if( $argc > 1 )
    {
        $type = $argv[ 1 ];
        if( array_key_exists($type, $proc) === false )
        {
            echo "Unknown method DIFF being used\n";
            $type = "diff";
        }
    }
    else
    {
        $type = "diff";
    }

    foreach( $config["dsn"] as $database => $dsn )
    {
        echo "Connecting to " . $database . "\n";
        try
        {
            //          echo  $config[ "data2_dsn" ].$year.sprintf('%02d',$month)."\n";
            $db = new PDO( $dsn, $config[ "db_username" ], $config[ "db_password" ] );
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch( Exception $e )
        {
            echo "Database connection failed\n";
            //die( $e->getMessage()."\n" );
            continue;
        }

        $stmt = $db->query( $proc[ $type ] );
        $base = "/root/sp/routines/" . $database;
        if( file_exists( $base ) === false )
        {
            if( mkdir( $base, 0777, true ) === false )
            {
                die( "Could not create folder" );
            }
        }

        foreach( $stmt->fetchAll() as $record )
        {
            chdir( $base );
            $location = $base . "/" . trim( $record["ROUTINE_NAME"] ) . ".sql";
            echo "Handling " . $location . "\n";
            file_put_contents($location, $record["ROUTINE_DEFINITION"]);

            if( $type == "dump" )
            {
                exec( "git init" );
            }
            else
            {
                exec( "git add ." );
                exec( "git commit -m 'changes for " . date("Y-m-d H:i") . "'" );
            }
        }
        $db = null;
    }


