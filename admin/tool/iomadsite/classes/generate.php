<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Gnerate site class
 *
 * @package tool_iomadsite
 * @copyright 2018 Howard Miller
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_iomadsite;

require_once($CFG->dirroot . '/admin/tool/generator/classes/backend.php');
use tool_generator_backend;
require_once($CFG->dirroot . '/lib/phpunit/classes/util.php');
use phpunit_util;
use context_module;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/iomad/lib/company.php');
require_once($CFG->dirroot . '/local/iomad/lib/user.php');
require_once($CFG->dirroot . '/course/lib.php');

class generate extends tool_generator_backend {
    /**
     * @var array Number of sections in course
     */
    private static $paramsections = array(1, 10, 100, 500, 1000, 2000);
    /**
     * @var array Number of assignments in course
     */
    private static $paramassignments = array(1, 10, 100, 500, 1000, 2000);
    /**
     * @var array Number of Page activities in course
     */
    private static $parampages = array(1, 50, 200, 1000, 5000, 10000);
    /**
     * @var array Number of students enrolled in course
     */
    private static $paramusers = array(1, 100, 1000, 10000, 50000, 100000);
    /**
     * Total size of small files: 1KB, 1MB, 10MB, 100MB, 1GB, 2GB.
     *
     * @var array Number of small files created in a single file activity
     */
    private static $paramsmallfilecount = array(1, 64, 128, 1024, 16384, 32768);
    /**
     * @var array Size of small files (to make the totals into nice numbers)
     */
    private static $paramsmallfilesize = array(1024, 16384, 81920, 102400, 65536, 65536);
    /**
     * Total size of big files: 8KB, 8MB, 80MB, 800MB, 8GB, 16GB.
     *
     * @var array Number of big files created as individual file activities
     */
    private static $parambigfilecount = array(1, 2, 5, 10, 10, 10);
    /**
     * @var array Size of each large file
     */
    private static $parambigfilesize = array(8192, 4194304, 16777216, 83886080,
            858993459, 1717986918);
    /**
     * @var array Number of forum discussions
     */
    private static $paramforumdiscussions = array(1, 10, 100, 500, 1000, 2000);
    /**
     * @var array Number of forum posts per discussion
     */
    private static $paramforumposts = array(2, 2, 5, 10, 10, 10);

    protected $companynames = [
        'Acme' => 'Acme Corporation',
        'Globex' => 'Globex Corporation',
        'Soylent' => 'Soylent Corporation',
        'Initech' => 'Initech',
        'Umbrella' => 'Umbrella Corporation',
        'Hooli' => 'Hooli',
        'Vehement' => 'Vehement Capital Partners',
        'Massive' => 'Massive Dynamic',
    ];

    protected $companysuffixes = [
        'Entertainment',
        'Systems',
        'Solutions',
        'Airways',
        'Pizza',
        'Cola',
        '& Evelyn',
        'Trucks',
        'Sporting Goods',
        'Mining',
        '& Young',
        'Networks',
        'Research',
        'Motor Company',
        'Media',
        'Global Group',
        'Aerospace',
        'Bay Company',
        'Technologies',
        'Electronics',
        'Communications',
        'Studios',
        'Software',
        'Financial',
        'Games',
        'Networks',
        'Guitars',
        'Chickens',
        'Brewery',
        'Digital',
    ];

    protected $citynames = [
        'Angel Grove',
        'Cabot Cove',
        'Mayberry',
        'Sunnydale',
        'Ambridge',
        'Landmark City',
        'Aberdale',
        'Danville',
        'Elmore',
        'Bedrock',
        'Springfield',
        'Quahog',
        'Castle Rock',
        'Hogsmeade',
        'Los Santos',
        'Waterdeep',
    ];

    protected $coursepre = [
        'Counter',
        'Audiology',
        'Planetary',
        'Foreign',
        'Siege',
        'Alien',
        'Earth',
        'Military',
        'Space',
        'Raid',
        'Self Defence',
        'Enhanced',
        'Small Forces',
        'Speech',
        'Dead Language',
        'Eurythmic',
        'Life',
        'Magic',
        'Physical',
        'Mount',
        'Disaster',
        'Stealth',
        'Ward',
    ];

    protected $coursepost = [
        'Social Sciences',
        'Literature',
        'Religion',
        'Handwriting',
        'Dialects',
        'Disaster Management',
        'Horse Riding',
        'Forensic Science',
        'Pathology',
        'Ethics',
        'Biology',
        'Language History',
        'Language Culture',
        'Arts',
        'Evolutionary Biology',
        'Drama',
        'History',
        'Strategy',
        'Psychology',
        'Finance',
        'Speech',
        'Linguistics',
        'Practice',
        'Healthcare Practice',
        'Creation',
        'Music',
        'Diplomacy',
        'Resource Management',
        'Nutrition',
        'Tactics',
        'Spellcasting',
    ];

    protected $courseextra = [
        'Advanced',
        'Further',
        'Second Year',
        'First Year',
        'Third Year',
        'Begginers',
        'Elementary',
        'An Introduction To',
        'Studies in',
    ];

    protected $firstnames;

    protected $lastnames;

    protected $licenseindex = 1;

    public function __construct($nb_users, $nb_courses, $size) {
        global $CFG;

        require_once($CFG->dirroot . '/admin/tool/iomadsite/firstnames.php');
        require_once($CFG->dirroot . '/admin/tool/iomadsite/lastnames.php');
        $this->firstnames = $firstnames;
        $this->lastnames = $lastnames;
        require_once($CFG->dirroot . '/lib/phpunit/classes/util.php');
        $this->generator = phpunit_util::get_data_generator();
        $this->fixeddataset = false;
        $this->size = $size;
        $this->nb_users = $nb_users;
        $this->nb_courses = $nb_courses   ;
    }

    /**
     * Make course name
     * @return array(shortname, fullname) 
     */
    protected function invent_coursename($tenant) {
 
        if (rand(0,10) < 4) {
            $extra = $this->courseextra[array_rand($this->courseextra,1)] . ' ';
        } else {
            $extra = '';
        }
        $coursepre = $this->coursepre[array_rand($this->coursepre,1)];
        $coursepost = $this->coursepost[array_rand($this->coursepost,1)];
        $fullname = $extra . $coursepre . ' ' . $coursepost;
        $shortname = $tenant.substr($coursepre, 0, 1) . substr($coursepost, 0, 1) . rand(10000, 99999);

        return [$shortname, $fullname];
    }

    /**
     * Make company category
     * @param string $fullname
     * @return int category id
     */
    protected function company_category($fullname) {
        global $DB;

        $coursecat = new \stdclass();
        $coursecat->name = $fullname;
        $coursecat->sortorder = 999;
        $coursecat->id = $DB->insert_record('course_categories', $coursecat);
        $coursecat->context = \context_coursecat::instance($coursecat->id);
        $categorycontext = $coursecat->context;
        $categorycontext->mark_dirty();
        $DB->update_record('course_categories', $coursecat);
        fix_course_sortorder();

        return $coursecat->id;
    }

    /**
     * Make profile for this company
     * @param string $shortname
     * @return int profile id
     */
    protected function company_profile($shortname) {
        global $DB;

        $catdata = new \stdclass();
        $catdata->sortorder = $DB->count_records('user_info_category') + 1;
        $catdata->name = $shortname;
        $profileid = $DB->insert_record('user_info_category', $catdata, false);

        return $profileid;
    }

    /**
     * Create company record
     * @param string $shortname
     * @param string $fullname 
     * @return object record
     */
    protected function company_record($shortname, $fullname) {
        global $DB;

        $company = new \stdClass();
        $company->name = $fullname;
        $company->shortname = $shortname;
        $company->city = $this->citynames[array_rand($this->citynames,1)];
        $company->country = 'GB';
        $company->maildisplay = 0;
        $company->mailformat = 1;
        $company->maildigest = 0;
        $company->autosubscribe = 1;
        $company->trackforums = 0;
        $company->htmleditor = 1;
        $company->screenreader = 0;
        $company->timezone = 99;
        $company->lang = 'en';
        $company->theme = 'iomadboost';
        $company->category = $this->company_category($fullname);
        $company->profileid = $this->company_profile($shortname);
        $company->suspended = 0;
        $company->emailprofileid = 0;
        $company->supervisorprofileid = 0;
        $company->managernotify = 0;
        $company->parentid = 0;
        $company->ecommerce = 0;
        $company->managerdigestday = 0;
        $company->previousroletemplateid = 0;

        $companyid = $DB->insert_record('company', $company);
        $company = $DB->get_record('company', ['id' => $companyid]);

        \company::initialise_departments($companyid);

        return $company;
    }

    /**
     * Add random courses to a company
     * @param object $company
     */
    public function courses($company) {

        // Iomad company object.
        $comp = new \company($company->id);
        
        // Add a random number of courses
        #$howmany = rand(10, 25);
        // Set 10 courses
        $howmany = $this->nb_courses;
        for ($i=0; $i < $howmany; $i++) {
            list($shortname, $fullname) = $this->invent_coursename($company->id);
            $data = new \stdClass();
            $data->fullname = $fullname;
            $data->shortname = $shortname;
            $data->category = $company->category;
            $data->numsections = self::$paramsections[$this->size];            
            $course = create_course($data);
            $comp->add_course($course, 0, true);
            $this->course = $course;
            mtrace("Created course '$fullname'");

            // Add some users
            $this->userids = array();
            $this->users($company, $shortname);
            
            mtrace("Assignments ");
            $this->create_assignments();
            mtrace("Pages ");
            $this->create_pages();
            mtrace("Small ");
            $this->create_small_files();
            mtrace("Big ");
            $this->create_big_files();

            $this->create_forum();

        }
    }

    /**
     * Create batch of licenses for company
     * @param int $companyid
     *
     */
    protected function licenses($companyid) {
        global $DB;

        $numberoflicenses = rand(10, 30);
        for ($i=0; $i < $numberoflicenses; $i++) {
            $licenseid = $this->create_license($companyid);
        }
    }

    /**
     * Create random license
     * 
     * @param int $companyid
     * @return int id
     */
    protected function create_license($companyid) {
        global $DB;

        $license = new \stdClass;
        $license->name = "License " . ++$this->licenseindex;
        $license->allocation = rand(10, 250);
        $license->validlength = rand(30, 365);
        $license->startdate = time();
        $license->expirydate = time() + 31557600;
        $license->used = 0;
        $license->companyid = $companyid;
        $license->parentid = 0;
        $license->program = 0;
        $license->reference = '';
        $license->instant = 0;
        $id = $DB->insert_record('companylicense', $license);

        return $id;
    }

    /**
     * Create random user
     * @param int $companyid
     * @param int $courseid;
     */
    protected function create_user($companyid, $courseid) {
        global $DB;

        $firstname = $this->firstnames[array_rand($this->firstnames, 1)];
        $lastname = $this->lastnames[array_rand($this->lastnames, 1)];
        $email = $companyid . "." . $firstname . '@example.com';
        $username = $companyid . "." . strtolower($firstname);
        
        // check existing user
        $userrec = $DB->get_record('user', array('username' => $username));
        
        if ($userrec = $DB->get_record('user', array('username' => $username))) {
            $userid = $userrec->id;
            mtrace("User $userid reused.");
        } else {

            // data object for user details
            $data = new \stdClass;
            $data->username = $username;
            $data->firstname = $firstname;
            $data->lastname = $lastname;
            $data->email = $email;
            $data->use_email_as_username = 0;
            $data->sendnewpasswordemails = 0;
            $data->preference_auth_forcepasswordchange = 0;
            $data->newpassword = 'moodle';
            $data->companyid = $companyid;
            $data->selectedcourses = [];
            $userid = \company_user::create($data);
            mtrace("User $userid created.");
        }

        mtrace("Assign course $courseid to user $userid");
        $this->userids[] = $userid;
        $userrec = $DB->get_record('user', array('id' => $userid));
        \company_user::enrol($userrec, array($courseid), $companyid, 0, 0);        


    }

    /**
     * Create users for course
     * @param object $company
     * @param string $shortname (of course)
     */
    public function users($company, $shortname) {
        global $DB;

        $course = $DB->get_record('course', ['shortname' => $shortname], '*', MUST_EXIST);
        #$howmany = rand(10, 40);
        $howmany = $this->nb_users;
        for ($i=1; $i < $howmany; $i++) {
            $this->create_user($company->id, $course->id);
        }
    }

    /**
     * Create the companies
     */
    public function companies() {
        global $DB;

        foreach ($this->companynames as $shortname => $fullname) {

            // Make sure it doesn't already exist.
            if (!$company = $DB->get_record('company', ['shortname' => $shortname])) {
                mtrace("Making company - $fullname");
                $company = $this->company_record($shortname, $fullname);
            }
            $this->courses($company);
        }
    }

    /**
     * Creates a number of Assignment activities.
     */
    private function create_assignments() {
        // Set up generator.
        $assigngenerator = $this->generator->get_plugin_generator('mod_assign');

        // Create assignments.
        $number = self::$paramassignments[$this->size];
        $this->log('createassignments', $number, true);
        for ($i = 0; $i < $number; $i++) {
            $record = array('course' => $this->course);
            $options = array('section' => $this->get_target_section());
            $assigngenerator->create_instance($record, $options);
            $this->dot($i, $number);
        }

        $this->end_log();
    }

    /**
     * Creates a number of Page activities.
     */
    private function create_pages() {
        // Set up generator.
        $pagegenerator = $this->generator->get_plugin_generator('mod_page');

        // Create pages.
        $number = self::$parampages[$this->size];
        $this->log('createpages', $number, true);
        for ($i = 0; $i < $number; $i++) {
            $record = array('course' => $this->course);
            $options = array('section' => $this->get_target_section());
            $pagegenerator->create_instance($record, $options);
            $this->dot($i, $number);
        }

        $this->end_log();
    }

    /**
     * Creates one resource activity with a lot of small files.
     */
    private function create_small_files() {
        $count = self::$paramsmallfilecount[$this->size];
        $this->log('createsmallfiles', $count, true);

        // Create resource with default textfile only.
        $resourcegenerator = $this->generator->get_plugin_generator('mod_resource');
        $record = array('course' => $this->course,
                'name' => get_string('smallfiles', 'tool_generator'));
        $options = array('section' => 0);
        $resource = $resourcegenerator->create_instance($record, $options);

        // Add files.
        $fs = get_file_storage();
        $context = context_module::instance($resource->cmid);
        $filerecord = array('component' => 'mod_resource', 'filearea' => 'content',
                'contextid' => $context->id, 'itemid' => 0, 'filepath' => '/');
        for ($i = 0; $i < $count; $i++) {
            $filerecord['filename'] = 'smallfile' . $i . '.dat';

            // Generate random binary data (different for each file so it
            // doesn't compress unrealistically).
            $data = random_bytes_emulate($this->limit_filesize(self::$paramsmallfilesize[$this->size]));

            $fs->create_file_from_string($filerecord, $data);
            $this->dot($i, $count);
        }

        $this->end_log();
    }

    /**
     * Creates a number of resource activities with one big file each.
     */
    private function create_big_files() {
        // Work out how many files and how many blocks to use (up to 64KB).
        $count = self::$parambigfilecount[$this->size];
        $filesize = $this->limit_filesize(self::$parambigfilesize[$this->size]);
        $blocks = ceil($filesize / 65536);
        $blocksize = floor($filesize / $blocks);

        $this->log('createbigfiles', $count, true);

        // Prepare temp area.
        $tempfolder = make_temp_directory('tool_generator');
        $tempfile = $tempfolder . '/' . rand();

        // Create resources and files.
        $fs = get_file_storage();
        $resourcegenerator = $this->generator->get_plugin_generator('mod_resource');
        for ($i = 0; $i < $count; $i++) {
            // Create resource.
            $record = array('course' => $this->course,
                    'name' => get_string('bigfile', 'tool_generator', $i));
            $options = array('section' => $this->get_target_section());
            $resource = $resourcegenerator->create_instance($record, $options);

            // Write file.
            $handle = fopen($tempfile, 'w');
            if (!$handle) {
                throw new coding_exception('Failed to open temporary file');
            }
            for ($j = 0; $j < $blocks; $j++) {
                $data = random_bytes_emulate($blocksize);
                fwrite($handle, $data);
                $this->dot($i * $blocks + $j, $count * $blocks);
            }
            fclose($handle);

            // Add file.
            $context = context_module::instance($resource->cmid);
            $filerecord = array('component' => 'mod_resource', 'filearea' => 'content',
                    'contextid' => $context->id, 'itemid' => 0, 'filepath' => '/',
                    'filename' => 'bigfile' . $i . '.dat');
            $fs->create_file_from_pathname($filerecord, $tempfile);
        }

        unlink($tempfile);
        $this->end_log();
    }

    /**
     * Creates one forum activity with a bunch of posts.
     */
    private function create_forum() {
        global $DB;

        $discussions = self::$paramforumdiscussions[$this->size];
        $posts = self::$paramforumposts[$this->size];
        $totalposts = $discussions * $posts;

        $this->log('createforum', $totalposts, true);

        // Create empty forum.
        $forumgenerator = $this->generator->get_plugin_generator('mod_forum');
        $record = array('course' => $this->course,
                'name' => get_string('pluginname', 'forum'));
        $options = array('section' => 0);
        $forum = $forumgenerator->create_instance($record, $options);

        // Add discussions and posts.
        $sofar = 0;
        for ($i = 0; $i < $discussions; $i++) {
            $record = array('forum' => $forum->id, 'course' => $this->course->id,
                    'userid' => $this->get_target_user());
            $discussion = $forumgenerator->create_discussion($record);
            $parentid = $DB->get_field('forum_posts', 'id', array('discussion' => $discussion->id), MUST_EXIST);
            $sofar++;
            for ($j = 0; $j < $posts - 1; $j++, $sofar++) {
                $record = array('discussion' => $discussion->id,
                        'userid' => $this->get_target_user(), 'parent' => $parentid);
                $forumgenerator->create_post($record);
                $this->dot($sofar, $totalposts);
            }
        }

        $this->end_log();
    }

    /**
     * Gets a section number.
     *
     * Depends on $this->fixeddataset.
     *
     * @return int A section number from 1 to the number of sections
     */
    private function get_target_section() {

        if (!$this->fixeddataset) {
            $key = rand(1, self::$paramsections[$this->size]);
        } else {
            // Using section 1.
            $key = 1;
        }

        return $key;
    }

    /**
     * Gets a user id.
     *
     * Depends on $this->fixeddataset.
     *
     * @return int A user id for a random created user
     */
    private function get_target_user() {

        if (!$this->fixeddataset) {
            $userid = $this->userids[rand(1, count($this->userids)-1)];
        } else if ($userid = current($this->userids)) {
            // Moving pointer to the next user.
            next($this->userids);
        } else {
            // Returning to the beginning if we reached the end.
            $userid = reset($this->userids);
        }

        return $userid;
    }

    /**
     * Restricts the binary file size if necessary
     *
     * @param int $length The total length
     * @return int The limited length if a limit was specified.
     */
    private function limit_filesize($length) {

        // Limit to $this->filesizelimit.
        if (is_numeric($this->filesizelimit) && $length > $this->filesizelimit) {
            $length = floor($this->filesizelimit);
        }

        return $length;
    }


}
