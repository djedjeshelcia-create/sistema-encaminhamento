<?php include 'conexao.php'; 
$sintomas = $conn->query("SELECT * FROM sintomas");
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Sistema de Encaminhamento</title>
<style>body{font-family:Arial; background:#f0f4f8; padding:30px} .box{background:white; max-width:600px; margin:auto; padding:25px; border-radius:12px; box-shadow:0 4px 15px #0002} h2{color:#0d47a1} button{background:#0d47a1; color:white; padding:12px 20px; border:none; border-radius:8px; cursor:pointer; width:100%; font-size:16px} .result{margin-top:20px; padding:15px; border-radius:8px; background:#e3f2fd} label{display:block; margin:8px 0}</style>
</head><body><div class="box">
<h2>🏥 Sistema de Encaminhamento</h2>
<p>Selecione os sintomas do paciente:</p>
<form method="POST">
<?php while($s = $sintomas->fetch_assoc()){ echo "<label><input type='checkbox' name='sintomas[]' value='{$s['codigo']}'> {$s['nome']}</label>"; } ?>
<button type="submit" name="avaliar">Avaliar Paciente</button>
</form>

<?php
if(isset($_POST['avaliar'])){
  $selecionados = $_POST['sintomas'] ?? [];
  if(empty($selecionados)){ echo "<div class='result'>Selecione pelo menos 1 sintoma</div>"; }
  else {
    $regras = $conn->query("SELECT * FROM regras");
    $encontrado = false;
    while($r = $regras->fetch_assoc()){
      $necessarios = explode(',', $r['sintomas_necessarios']);
      $temTodos = true;
      foreach($necessarios as $n){ if(!in_array(trim($n), $selecionados)) $temTodos = false; }
      if($temTodos){
        echo "<div class='result' style='border-left:5px solid red'><b>✅ ENCAMINHAMENTO:</b><br>Especialidade: <b>{$r['especialidade']}</b><br>Sala: <b>{$r['sala']}</b><br>Prioridade: <b>{$r['prioridade']}</b><br>Regra: {$r['regra_nome']}<br><small>{$r['justificativa']}</small></div>";
        $encontrado = true; break;
      }
    }
    if(!$encontrado) echo "<div class='result'>Nenhuma regra específica, encaminhar para triagem geral - Sala 1</div>";
  }
}
?>
</div></body></html>