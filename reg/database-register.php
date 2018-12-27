<?php
    /*--
    COMPANY: ezdev (former: the3ds)
    AUTHOR: Tom DINH
    PROJECT: mirana core v2
    DESCRIPTION:
        Register database for Mirana to use
        Database has not registered here will not able to use in application(s)

    --*/

    Mirana_Database::add("testdb",
        [
            DB_TYPE => MYSQL,
            DB_URL => "localhost",
            DB_NAME => "test",
            DB_PORT => "3306",
            DB_USER => "the3ds",
            DB_PASS => "smartmind"
        ]
    );
?>
