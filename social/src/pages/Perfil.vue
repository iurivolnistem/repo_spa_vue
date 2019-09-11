<template>
  <TemplateSite>
    <div slot="side">
      <div class="perfil">
        <img :src="usuario.imagem" />
        <div class="file-field btn-floating halfway-fab waves-effect waves-light red">
          <i class="material-icons">file_upload</i>
          <input type="file" v-on:change="salvaImagem" value="Imagem" />
        </div>
      </div>
    </div>
    <div slot="principal">
      <div class="card center-align">
        <div class="row">
          <div class="col s12">
            <h3>Perfil</h3>
          </div>
        </div>
        <div class="row">
          <div class="col s6">
            <input type="text" placeholder="Nome" v-model="name" />
          </div>
          <div class="col s6">
            <input type="email" placeholder="E-mail" v-model="email" />
          </div>
        </div>
        <div class="row">
          <div class="col s6">
            <input type="password" placeholder="Senha" v-model="password" />
          </div>
          <div class="col s6">
            <input type="password" placeholder="Confirme a senha" v-model="password_confirmation" />
          </div>
        </div>
        <button class="btn waves-effect green" v-on:click="perfil">Atualizar</button>
      </div>
    </div>
  </TemplateSite>
</template>

<script>
import TemplateSite from "@/Templates/TemplateSite";

export default {
  name: "Perfil",
  data() {
    return {
      usuario: false,
      name: "",
      email: "",
      password: "",
      password_confirmation: "",
      imagem: ""
    };
  },
  created() {
    let usuarioAux = this.$store.getters.getUsuario;
    if (usuarioAux) {
      this.usuario = this.$store.getters.getUsuario;
      this.name = this.usuario.name;
      this.email = this.usuario.email;
    }
  },
  components: {
    TemplateSite
  },
  methods: {
    salvaImagem(e) {
      let arquivo = e.target.files || e.dataTransfer.files;
      if (!arquivo.length) {
        return;
      }

      let reader = new FileReader();
      reader.onloadend = e => {
        this.imagem = e.target.result;
      };
      reader.readAsDataURL(arquivo[0]);
    },
    perfil() {
      this.$http
        .put(
          this.$urlAPI + `perfil`,
          {
            name: this.name,
            email: this.email,
            imagem: this.imagem,
            password: this.password,
            password_confirmation: this.password_confirmation
          },
          {
            headers: { authorization: "Bearer " + this.$store.getters.getToken }
          }
        )
        .then(response => {
          if (response.data.status) {
            this.usuario = response.data.usuario;
            this.$store.commit("setUsuario", response.data.usuario);
            sessionStorage.setItem("usuario", JSON.stringify(this.usuario));
            alert("Perfil atualizado!");
          } else if (response.data.status == false && response.data.validacao) {
            let erros = "";
            for (let erro of Object.values(response.data.erros)) {
              erros += erro + " ";
            }
            alert(erros);
          }
        })
        .catch(e => {
          console.log(e);
          alert("Erro! Tente novamente mais tarde!");
        });
    }
  }
};
</script>

<style scoped>
h1 {
  margin: 0 0 20px;
}
.perfil {
  height: 200px;
  background: #fff;
  margin: 1em 0;
  box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14),
    0 3px 1px -2px rgba(0, 0, 0, 0.12), 0 1px 5px 0 rgba(0, 0, 0, 0.2);
  position: relative;
}
.perfil img {
  width: 100%;
  object-fit: cover;
  max-height: 200px;
  height: 100%;
}
.perfil .user-infos {
  text-align: center;
  margin-top: 20px;
}
.perfil .user-infos .user-name {
  color: #ee6e73;
  font-weight: 700;
  font-size: 16px;
  margin-bottom: 5px;
}
.card {
  padding: 20px;
  margin: 1em 0 1rem 0;
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
