<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once '../controller/Controller.php';
$controller = new Controller();
$groupId = intval($_GET['group_id']);
$groupDetails = $controller->getGroupDetails($groupId);
$messages = $controller->getNewsByGroup($groupId);
$members = $controller->getGroupMembers($groupId);
$creatorId = $groupDetails['creator_id'];

if (!$groupDetails) {
    header('Location: group_list.php'); // Redirection vers la liste des groupes
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discussion de Groupe</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .message-list {
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="../discussion.php"><img src="../logo.png" alt="Logo" style="height: 30px;"></a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../discussion.php">Accueil</a>
                </li>
            </ul>
            <span class="navbar-text mr-3">
                <?php if (isset($_SESSION['username'])): ?>
                    Connecté en tant que: <?= htmlspecialchars($_SESSION['username']) ?>
                <?php else: ?>
                    Utilisateur non connecté
                <?php endif; ?>
            </span>
            <a href="../logout.php" class="btn btn-outline-danger">Déconnexion</a>
        </div>
    </nav>
    <div class="container mt-5">
        <h2 class="text-center">Discussion du Groupe: <?= htmlspecialchars($groupDetails['name']) ?></h2>
        <p class="text-center">Créateur: <?= htmlspecialchars($groupDetails['creator']) ?></p>
        <h4>Membres du Groupe:</h4>
        <ul class="list-group mb-3">
            <?php foreach ($members as $member): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= htmlspecialchars($member['username']) ?>
                    <div>
                        <?php if ($creatorId !== $member['id'] && $creatorId === $_SESSION['user_id']): ?>
                            <?php if ($member['status'] === 'active'): ?>
                                <a href="../suspend_member.php?group_id=<?= $groupId ?>&user_id=<?= $member['id'] ?>" class="btn btn-warning btn-sm">Suspendre</a>
                            <?php else: ?>
                                <a href="../restore_member.php?group_id=<?= $groupId ?>&user_id=<?= $member['id'] ?>" class="btn btn-success btn-sm">Rétablir</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
        <h4>Messages:</h4>
        <div class="message-list">
            <ul class="list-group mb-3">
                <?php foreach (array_reverse($messages) as $message): ?>
                    <li class="list-group-item">
                        <strong><?= htmlspecialchars($message['username']) ?>:</strong> <?= htmlspecialchars($message['content']) ?>
                        <div class="text-right">
                            <small><?= htmlspecialchars($message['created_at']) ?></small>
                            <?php if ($message['user_id'] === $_SESSION['user_id']): ?>
                                <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editModal" data-message-id="<?= $message['id'] ?>" data-message-content="<?= htmlspecialchars($message['content']) ?>">Modifier</button>
                                <a href="../delete_message.php?message_id=<?= $message['id'] ?>&group_id=<?= $groupId ?>" class="btn btn-sm btn-danger">Supprimer</a>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Modifier le Message</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="editMessageForm" action="../edit_message.php" method="post">
                            <input type="hidden" name="message_id" id="modalMessageId">
                            <input type="hidden" name="group_id" value="<?= $groupId ?>">
                            <div class="form-group">
                                <textarea class="form-control" name="content" id="modalMessageContent" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <h4>Envoyer un Message:</h4>
        <?php
        if ($_SESSION['user_id'] === $creatorId || array_search($_SESSION['user_id'], array_column($members, 'id')) !== false) {
            $currentUser = array_filter($members, function($member) {
                return $member['id'] === $_SESSION['user_id'];
            });
            $currentUser = reset($currentUser);
            if ($currentUser['status'] === 'active') {
        ?>
        <form action="../send_message.php" method="post">
            <input type="hidden" name="group_id" value="<?= $groupId ?>">
            <div class="form-group">
                <textarea class="form-control" name="message" rows="3" placeholder="Écrire un message..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Envoyer</button>
        </form>
        <?php
            } else {
                echo "<p class='text-danger'>Vous êtes suspendu et ne pouvez pas envoyer de messages.</p>";
            }
        }
        ?>
        <div class="text-right mb-3">
            <a href="add_member.php?group_id=<?= $groupId ?>" class="btn btn-secondary">Ajouter un Membre</a>
            <a href="../restore_messages.php" class="btn btn-info">Restaurer les Messages</a>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $('#editModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var messageId = button.data('message-id');
            var messageContent = button.data('message-content');

            var modal = $(this);
            modal.find('#modalMessageId').val(messageId);
            modal.find('#modalMessageContent').val(messageContent);
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var messageList = document.querySelector('.message-list');
            messageList.scrollTop = messageList.scrollHeight;
        });
    </script>
</body>
</html>
