<template>
  <TemplateLogin>
    <div class="card center-align">
      <div class="row">
        <div class="col s12">
          <h3>Cadastre-se</h3>
        </div>
      </div>
      <div class="row">
        <div class="col s6">
          <input type="text" placeholder="Nome" v-model="nome" />
        </div>
        <div class="col s6">
          <input type="email" placeholder="E-mail" v-model="email" />
        </div>
      </div>
      <div class="row">
        <div class="col s6">
          <input type="password" placeholder="Senha" v-model="senha" />
        </div>
        <div class="col s6">
          <input type="password" placeholder="Confirme a senha" v-model="confirma" />
        </div>
      </div>
      <div class="row">
        <div class="col s12">
          <button class="btn waves-effect green" v-on:click="cadastro">Enviar</button>
          <router-link class="btn waves-effect orange" to="/Login">Já tenho conta</router-link>
        </div>
      </div>
    </div>
  </TemplateLogin>
</template>

<script>
import TemplateLogin from "@/Templates/TemplateLogin";
import Grid from "@/components/Estaticos/Grid";

export default {
  name: "Login",
  components: { TemplateLogin, Grid },
  data() {
    return {
      nome: "",
      email: "",
      senha: "",
      confirma: ""
    };
  },
  methods: {
    cadastro() {
      this.$http
        .post(this.$urlAPI + `cadastro`, {
          name: this.nome,
          email: this.email,
          password: this.senha,
          password_confirmation: this.confirma
        })
        .then(response => {
          if (response.data.status) {
            this.$store.commit("setUsuario", response.data.usuario);
            sessionStorage.setItem(
              "usuario",
              JSON.stringify(response.data.usuario)
            );
            this.$router.push("/Home");
          } else if (
            (response.data.status = false && response.data.validacao)
          ) {
            let erros = "";
            for (let erro of Object.values(response.data.erros)) {
              erros += erro + " ";
            }
            alert(erros);
          } else {
            alert("Erro ao efetuar o cadastro! Tente novamente mais tarde.");
          }
        })
        .catch(e => {
          console.log(e);
        });
    }
  }
};
</script>
<style scoped>
.card {
  padding: 20px;
  width: 50%;
  margin: 0 auto;
}
.card h3 {
  margin: 0;
  font-weight: 700;
  text-transform: uppercase;
  text-align: center;
  background: #43a06f;
  color: #fff;
  padding: 15px 0;
}
</style>
