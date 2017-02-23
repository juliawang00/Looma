<!doctype html>
<!--
Author: Skip
Filename: looma-lesson-present.php
Date: 02/2017
Description: looma lesson plan presenter

-->
	<?php $page_title = 'Looma Lesson Presenter ';
          include ('includes/header.php');
          require ('includes/mongo-connect.php');
          include ('includes/activity-button.php'); ?>

    <link rel="stylesheet" href="css/looma-media-controls.css">
    <link rel="stylesheet" href="css/looma-video.css">
    <link rel="stylesheet" href="css/looma-lesson-present.css">

  </head>

  <body>
    <?php
        //Gets the filename, filepath, and the thumbnail location
        if (isset($_REQUEST['id'])) $lesson_id = $_REQUEST['id'];
    ?>

    <script>
        //send "id" parameter to client-side JS
        var lesson_id = "<?php echo $lesson_id ?>";
    </script>

    <div id="main-container-horizontal">

        <div id="viewer"></div>
        <div id="timeline-container">

        <div id="timeline" >

        <!--
            <span>Timeline: </span><span class="filename"></span>
        -->
        <?php

        function prefix ($ch_id) { // extract textbook prefix from ch_id
            preg_match("/^([1-8](EN|SS|M|S|N))[0-9]/", $ch_id, $matches);
            return $matches[1];
        };

        //look up the lesson plan in mondo lessons collection
        //send DN, AUTHOR and DATE in a hidden DIV
        //for each ACTIVITY in the DATA field of the lesson, create an 'activity button' in the timeline

            //get the mongo document for this lesson
            $query = array('_id' => new MongoID($lesson_id));
            //returns only these fields of the activity record
            $projection = array('_id' => 0,
                                'dn' => 1,
                                'author' => 1,
                                'date' => 1,
                                'dn' => 1,
                                'data' => 1
                                );

            $lesson = $lessons_collection -> findOne($query, $projection);

            $data = $lesson['data'];
            $displayname = $lesson['dn'];

            //should send DN, AUTHOR and DATE in a hidden DIV

            foreach ($data as $activity) {

                //echo "ID is " . $activity['id'];
                //echo "coll is " . $activity['collection'];

                if ($activity['collection'] == 'activities') {

                    $query = array('_id' => new MongoID($activity['id']));

                    $db_collection =  $activities_collection;
                    $details = $db_collection -> findOne($query);

                    //  format is:  makeActivityButton($ft, $fp, $fn, $dn, $thumb, $ch_id, $mongo_id, $url, $pg, $zoom)

                        makeActivityButton($details['ft'],
                                           (isset($details['fp'])) ? $details['fp'] : null,
                                           (isset($details['fn'])) ? $details['fn'] : null,
                                           (isset($details['dn'])) ? $details['dn'] : null,
                                           (isset($details['fn'])) ? thumbnail($details['fn']) : null,
                                           (isset($details['ch_id'])) ? $details['ch_id'] : null,
                                           $activity['id'],
                                           (isset($details['url'])) ? $details['url'] : null,
                                           null,
                                           null);
                } else

                if ($activity['collection'] == 'chapters') {

                    $query = array('_id' => $activity['id']);
                    $chapter = $chapters_collection -> findOne($query);

                    $query = array('prefix' => prefix($chapter['_id']));

                    $textbook = $textbooks_collection -> findOne($query);

                    // makeActivityButton($ft, $fp, $fn, $dn, $thumb, $ch_id, $mongo_id, $url, $pg, $zoom)

                        makeActivityButton('pdf',
                                           (isset($textbook['fp'])) ? '../content/' . $textbook['fp'] : null,
                                           (isset($textbook['fn'])) ? $textbook['fn'] : null,
                                           (isset($chapter['dn'])) ? $chapter['dn'] : null,
                                           (isset($textbook['fn'])) ? thumbnail($textbook['fn']) : null,
                                           $chapter['_id'],
                                           null,
                                           null,
                                           (isset($chapter['pn']) ? $chapter['pn'] : 1),
                                           160);
                };
             };
           ?>
        </div>
        </div>

         <div id="title">
            <span>Looma Lesson:&nbsp; <span class="filename"><?php echo $displayname ?></span></span>
        </div>

    </div>

    <div id="controlpanel">

        <div id="button-box">
            <button class="control-button" id="back">
                <!-- <img src="images/back-arrow.png"> -->
            </button>
            <button class="control-button" id="pause">
               <!-- <img src="images/pause-button.png"> -->
            </button>
            <button class="control-button" id="forward">
                <!-- <img src="images/forward-arrow.png"> -->
            </button>
             <button class='control-button' id='dismiss' >
                <!-- <img src="images/delete-icon.png"> -->
            </button>
        </div>
    </div>

    <button  id="fullscreen-control"></button>

    <?php //include ('includes/toolbar.php'); ?>
    <?php include ('includes/js-includes.php'); ?>
    <script src="js/jquery-ui.min.js">  </script>
    <script src="js/looma-screenfull.js"></script>
     <script src="js/looma-media-controls.js"></script>
     <script src="js/looma-lesson-present.js"></script>
 </body>
</html>