<?php
if (!isset($dati)) require_once 'utility.php';
$datatable = true;
if (isAdminUserAutenticate() && isset($reset) && !isAdmin($dati['database'], $reset)) {
    $password = random(5);
    $name = $dati['database']->get('persone', 'nome', array ('id' => $reset));
    $username = str_replace(" ", "", strtolower($name));
    while (!isUserFree($dati['database'], $username, $reset)) {
        $username .= rand(0, 999);
    }
    $dati['database']->update('persone', 
            array ('username' => $username, 'password' => $password, 'email' => '', 'stato' => 0, 'verificata' => 0), 
            array ('AND' => array ('id' => $reset, 'stato[!]' => 0)));
    echo 'Username: ' . $username . ' - Password: ' . $password;
}
else {
    $scuole = $dati['database']->select('scuole', '*', array ('ORDER' => 'id'));
    $accessi = $dati['database']->select('accessi', '*');
    $numero = pieni($accessi, 'id');
    $admins = $dati['database']->select('admins', '*', array ('ORDER' => 'id'));
    $studenti = $dati['database']->select('studenti', '*', array ('id' => $dati['database']->max('studenti', 'id'), 'ORDER' => 'persona'));
    $classi = $dati['database']->select('classi', '*', array ('ORDER' => 'id'));
    $results = $dati['database']->select('persone', '*', array ('ORDER' => 'nome'));
    $pageTitle = 'Utenti registrati';
    require_once 'templates/shared/header.php';
    echo '
            <div class="jumbotron green">
                <div class="container text-center">
                    <h1><i class="fa fa-user-secret"></i> ' . $pageTitle . '</h1>
                </div>
            </div>
            <div class="jumbotron">
                <div class="container">
                <p>Elenco utenti registrati</p>
                <table class="table table-hover scroll">
                        <thead>
                            <tr>
                                <th>Cognome e nome</th>
                                <th>Classe</th>
                                <th>Scuola</th>
                                <th>Numero di accessi</th>';
    if (isAdminUserAutenticate()) {
        echo '
                                <th>Credenziali</th>';
    }
    echo '
                                <th>Profilo</th>
                            </tr>
                        </thead>
                        <tbody>';
    if ($results != null) {
        foreach ($results as $result) {
            $classe = '';
            $scuola = '';
            $studente = ricerca($studenti, $result['id'], 'persona');
            if ($studente != -1) {
                $class = ricerca($classi, $studenti[$studente]['classe']);
                if ($class != -1) {
                    $classe = $classi[$class]['nome'];
                }
                $scuola = $scuole[ricerca($scuole, $classi[$class]['scuola'])]['nome'];
            }
            else {
                $classe = 'Nessuna classe';
            }
            $cont = 0;
            if ($result['id'] - $numero[0] >= 0 && $numero[1] - $result['id'] >= 0 && $numero[2][$result['id'] - $numero[0]] != '') {
                $cont = $numero[2][$result['id'] - $numero[0]];
            }
            echo '
                            <tr>
                                <td>' . $result['nome'] . '</td>
                                <td>' . $classe . '</td>
                                <td>' . $scuola . '</td>
                                <td>' . $cont . '</td>';
            if (isAdminUserAutenticate()) {
                if (ricerca($admins, $result['id']) == -1) {
                    if ($result['stato'] != 0) {
                        echo '
                                <td id="cred">
                                    <span class="hidden" id="value">' . $result['id'] . '</span>
                                    <a class="btn btn-danger" id="reset">Reset credenziali</a>
                                </td>';
                    }
                    else {
                        echo '
                                <td>Username: ' . $result['username'] . ' - Password: ' . strtolower($result['password']) . '</td>';
                    }
                }
                else {
                    echo '
                                <td>Amministratore!!!</td>';
                }
            }
            echo '
                                <td><a class="btn btn-success" href="' . $dati['root'] . 'profilo/' . $result['id'] . '">Profilo</a></td>
                            </tr>';
        }
    }
    echo '
                        </tbody>
                    </table>
                </div>
            </div>';
    require_once 'templates/shared/footer.php';
}
?>