<?php
if(!is_object($libGlobal) || !$libAuth->isLoggedin())
	exit();

/*
* Context
*/

$commentId = isset($_REQUEST['commentid']) ? $_REQUEST['commentid'] : '';
$discussionId = isset($_REQUEST['discussionid']) ? $_REQUEST['discussionid'] : '';

if(!is_numeric($commentId) && !is_numeric($discussionId))
	$mode = 1; //new discussion
elseif(!is_numeric($commentId) && is_numeric($discussionId))
	$mode = 2; //new comment on existing discussion
elseif(is_numeric($commentId)) //edit comment on existing discussion
	$mode = 3;


if($mode == 1)
	echo '<h1>Neue Diskussion anlegen</h1>';
elseif($mode == 2)
	echo '<h1>Diskussionsbeitrag hinzufügen</h1>';
elseif($mode == 3)
	echo '<h1>Diskussionsbeitrag editieren</h1>';

if($mode == 2){
	$cmd = sprintf('SELECT * FROM mod_forum_thread WHERE id = %s',
		$libDb->secInp($discussionId));
	$row = $libDb->queryArray($cmd);
}
elseif($mode == 3){
	$cmd = sprintf('SELECT * FROM mod_forum_thread, mod_forum_comment WHERE mod_forum_thread.id = mod_forum_comment.thread_id AND mod_forum_comment.id = %s',
		$libDb->secInp($commentId));
	$row = $libDb->queryArray($cmd);
}


/*
* Output
*/
echo '<form method="post" action="index.php?pid=intranet_forum_thread">'."\n";
if($mode == 1)
	echo '<input type="text" name="title" size="30" /> Titel<br /><br />';
elseif($mode == 2 || $mode == 3)
	echo 'Diskussionstitel: ' .$row['title'] . '<br /><br />';

echo 'Text <br />';
echo '<textarea name="text" cols="60" rows="20">';

if($mode == 3)
	echo $row['text'];

echo '</textarea><br />'."\n";

if($mode == 2)
	echo '<input type="hidden" name="id" value="' .$discussionId. '" />';
if($mode == 3)
	echo '<input type="hidden" name="commentid" value="' .$commentId. '" />';

echo '<input type="hidden" name="action" value="save" />';

echo '<input type="submit" value="speichern">'."\n";
echo '</form>'."\n";


/*
* bisherige Diskussion
*/
if($mode == 2){
	$cmd = sprintf('SELECT * FROM mod_forum_comment WHERE thread_id = %s ORDER BY created DESC',
		$libDb->secInp($discussionId));
	$result = $libDb->query($cmd);

	echo '<h3>bisherige Diskussionbeiträge</h3>';
	
	echo '<table style="width:100%">';
	while($row = mysql_fetch_array($result)){
		echo '<tr id="' .$row['id']. '">';
		echo '<td style="width:15%">' . $libTime->formatDateTimeString($row['created'], 2);
		if($row['edited'] != '')
			echo '<br /><br />editiert: ' .$libTime->formatDateTimeString($row['edited'], 2);

		echo '<br /><br /></td>';
		echo '<td style="width:70%">' . nl2br(trim($row['text']));
		echo '<br /><br /></td>';
		echo '<td style="width:15%">' .$libMitglied->getMitgliedSignature($row['person_id'], "right"). '<br /><br /></td>';
		echo '</tr>';
	}
	echo '</table>';
}
?>