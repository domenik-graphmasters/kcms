<?php
if(!is_object($libGlobal) || !$libAuth->isLoggedin())
	exit();

$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
$commentId = isset($_REQUEST['commentid']) ? $_REQUEST['commentid'] : '';


/*
* Actions
*/
if(isset($_POST['action']) && $_POST['action'] == 'save' && isset($_REQUEST['text']) && $_REQUEST['text'] != ''){
	if(!is_numeric($commentId) && !is_numeric($id)){
		$cmd = sprintf('INSERT INTO mod_forum_thread (title, created) VALUES (%s, NOW())',
			$libDb->secInp($_REQUEST['title']));
		$libDb->query($cmd);
		
		$id = $libDb->queryAttribute('SELECT LAST_INSERT_ID()');
	
		$cmd = sprintf('INSERT INTO mod_forum_comment (thread_id, created, person_id, text) VALUES (LAST_INSERT_ID(), NOW(), %s, %s)',
			$libDb->secInp($libAuth->getId()),
			$libDb->secInp($_REQUEST['text']));
		$libDb->query($cmd);
		
		$libGlobal->notificationTexts[] = 'Der Beitrag wurde gespeichert.';
	}
	elseif(!is_numeric($commentId) && is_numeric($id)){
		$cmd = sprintf('INSERT INTO mod_forum_comment (thread_id, created, person_id, text) VALUES (%s, NOW(), %s, %s)',
			$libDb->secInp($id),
			$libDb->secInp($libAuth->getId()),
			$libDb->secInp($_REQUEST['text']));
		$libDb->query($cmd);
		
		$commentId = $libDb->queryAttribute('SELECT LAST_INSERT_ID()');
		
		$libGlobal->notificationTexts[] = 'Der Beitrag wurde gespeichert.';
	}
	elseif(is_numeric($commentId)){
		$cmd = sprintf('UPDATE mod_forum_comment SET edited = NOW(), text = %s WHERE id=%s AND person_id = %s',
			$libDb->secInp($_REQUEST['text']),
			$libDb->secInp($commentId),
			$libDb->secInp($libAuth->getId()));
		$libDb->query($cmd);
		
		$cmd = sprintf('SELECT thread_id FROM mod_forum_comment WHERE id = %s',
			$libDb->secInp($commentId));
		$id = $libDb->queryAttribute($cmd);
		
		$libGlobal->notificationTexts[] = 'Der Beitrag wurde gespeichert.';
	}
}
	
	
/*
* Output
*/

$cmd = sprintf('SELECT * FROM mod_forum_thread WHERE id = %s',
	$libDb->secInp($id));
$row = $libDb->queryArray($cmd);

echo '<h1>' .$row['title']. '</h1>';

echo $libString->getErrorBoxText();echo $libString->getNotificationBoxText();

echo '<p><a href="index.php?pid=intranet_forum_comment&amp;discussionid=' .$row['id']. '">antworten</a></p>';


$cmd = sprintf('SELECT * FROM mod_forum_comment WHERE thread_id = %s ORDER BY created ASC',
	$libDb->secInp($id));
$result = $libDb->query($cmd);

echo '<table style="width:100%">';
while($row = mysql_fetch_array($result)){
	echo '<tr id="' .$row['id']. '">';
	echo '<td style="width:15%">' . $libTime->formatDateTimeString($row['created'], 2);
	if($row['edited'] != '')
		echo '<br /><br />editiert: ' . $libTime->formatDateTimeString($row['edited'], 2);
	
	echo '<br /><br /></td>';
	echo '<td style="width:70%">' . nl2br(trim($row['text']));
	if($row['person_id'] == $libAuth->getId())
		echo '<br /><br /><a href="index.php?pid=intranet_forum_comment&amp;commentid=' .$row['id']. '">editieren</a>';
	echo '<br /><br /></td>';
	echo '<td style="width:15%">' .$libMitglied->getMitgliedSignature($row['person_id'], "right"). '<br /><br /></td>';
	
	echo '</tr>';
}
echo '</table>';
?>