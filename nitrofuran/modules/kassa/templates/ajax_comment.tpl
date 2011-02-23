<? foreach($_comments as $comment): ?>
	<span class="comment" onclick="setComment(this.innerHTML)"><?= htmlspecialchars($comment); ?></span>
<? endforeach; ?>