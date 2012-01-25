<?php
function hashpass($password) {
  return crypt($password, '$2a$07$hyperagressive123jkasdfakrjtwkrjaskfjasdkfjaf$');
}

function get_con() {
  $con = mysql_pconnect("sql.mit.edu","rcoh","rcoh");
  mysql_select_db("rcoh+gradschool", $con);
  if (!$con)
  {
    die('Could not connect: ' . mysql_error());
  }
  return $con;
}

function query_or_die($query, $con) {
  $result = mysql_query($query, $con);
  if(!$result){
    die('Error: ' . mysql_error());
  } else {
    return $result;
  } 
}

function go($loc) {
  header("Location: " . $loc);
}
function go_home() {
  header("Location: index.php");
}

function add_user($email, $hashpass, $con) { 
  $query = sprintf("insert into users (password, email) values('%s', '%s')",
          mysql_real_escape_string($hashpass),
          mysql_real_escape_string($email));
  $query_result = query_or_die($query, $con);
  if($query_result) {
    $result = mysql_fetch_array(query_or_die("select max(id) from users", $con));
    return $result[0]; 
  }
}


function filtered_search($query, $params, $uid, $con) {
  $cols = array("prof.id", "name", "school", "department", "image");
  return query_or_die(build_query_string($cols, $query, $params, $uid), $con);
}

function set_starred($prof_id, $user_id, $state, $con) {
  if($state == "true") {
    $stmnt = "insert into bookmarked_professors (prof_id, user_id) values($prof_id, $user_id)";
  } else {
    $stmnt = "delete from bookmarked_professors where prof_id = '$prof_id' and user_id = '$user_id'";
    echo $stmnt;
  }
  return query_or_die($stmnt, $con);
}

/** ,'s delimit OR queries, otherwise its AND **/
function process_search_term($search_term) {
  $terms = explode(",", $search_term);
  $result_string = '';
  foreach($terms as $term) {
    $result_string .= '\"' . $term . '\" ';
  }
  return trim($result_string);
}
function build_query_string($cols, $search_term, $params, $user_id = NULL) {
  if($user_id) {
    array_push($cols, "prof.id in (select prof_id from bookmarked_professors where user_id = $user_id) as starred");
    return build_query_string($cols, $search_term, $params);
  }
  $search_term = process_search_term($search_term);
  $where_queries = "";
  foreach ($params as $filter => $value_list) {
    $vals = explode(",", $value_list);
    $possible_values = "'" . join('\',\'', $vals) . "'";  
    $where_queries .= " and $filter in ($possible_values)";
  }
  $col_terms = implode(", ", $cols);   
  $stmnt="select " . $col_terms . " from keywords 
    inner join keywordmap on keywords.id=keywordmap.keyword_id 
    join prof on prof.id = keywordmap.prof_id 
    where match (keyword) against ('$search_term' in boolean mode) 
    $where_queries
    union 
    select distinct $col_terms from prof 
    where match(research_summary) against('$search_term' in boolean mode)
    $where_queries";
  return $stmnt;

}

function get_professor_distribution($col, $search_term, $params, $con) {
  $cols = array($col, "prof.id");
  $query = 
    "select $col, count(*) from (" . 
      build_query_string($cols, $search_term, $params) . 
    ") as T group by $col order by count(*) desc";
  return query_or_die($query, $con);
}
function prof_by_id($prof_id, $con) {
  $stmnt="select * from prof where id='$prof_id'";
  return query_or_die($stmnt, $con);
}

function research_interests($prof_id, $con) {
  $stmnt = "select distinct keyword from keywords inner join keywordmap on 
    keywords.id=keywordmap.keyword_id join prof 
    on prof.id = keywordmap.prof_id where prof.id=$prof_id;";
  return query_or_die($stmnt, $con);
}

function email_exists($email, $con) {
  $dbemails = mysql_query("SELECT * FROM users WHERE email='$email'", $con);
  return (mysql_num_rows($dbemails) > 0);
}

function get_distinct($col, $con) {
  $stmnt = "select distinct $col, count(*) from prof group by $col order by count(*) desc";
  return query_or_die($stmnt, $con);
}

function new_anon_user($con) {
  $stmnt = "select max(id) from users";
  $result = mysql_fetch_array(query_or_die($stmnt, $con));
  $result = $result[0] + 1;
  $stmnt = "insert into users (email) values($result)";
  query_or_die($stmnt, $con);
  return $result;
}

function merge_users($old_uid, $new_uid) {
  $stmnt = "update bookmarked_professors set user_id=" . mysql_real_escape_string($new_uid) . " where user_id=$old_uid";
  //TODO: add saved searches when added
  return query_or_die($stmnt, get_con());
}

function delete_user($uid) {
  $stmnt = "delete from users where id=$uid";
  return query_or_die($stmnt, get_con());
}

function research_interests_str($prof_id, $con, $search_string) {
  $result=research_interests($prof_id, $con);
  $first = mysql_fetch_array($result);
  if ($first) {
    $output = $first['keyword'];
    if ($output == $search_string) {
      $output = '<b>' . $output . '</b>';
    }
    while ($interest = mysql_fetch_array($result)) {
      if ($interest['keyword'] == $search_string) {
        $output = '<b>' . $interest['keyword'] . '</b>, ' . $output;
      } else {
        $output = $output . ', ' . $interest['keyword'];
      }
    }
  } else {
    $output = 'None listed.';
  }
  return $output;
}
/*The following credit to dev-tips.com*/
function remove_item_by_value($array, $val = '', $preserve_keys = true) {
  if (empty($array) || !is_array($array)) return false;
  if (!in_array($val, $array)) return $array;

  foreach($array as $key => $value) {
    if ($value == $val) unset($array[$key]);
  }

  return ($preserve_keys === true) ? $array : array_values($array);
}
?>
