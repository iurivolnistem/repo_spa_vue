<template>
  <span>
    <header>
      <Menu url="/Home">
        <li v-if="!usuario">
          <router-link to="/Login">Login</router-link>
        </li>
        <li v-if="!usuario">
          <router-link to="/Cadastro">Cadastre-se</router-link>
        </li>
        <li v-if="usuario">
          <router-link to="/Perfil">{{usuario.name}}</router-link>
        </li>
        <li v-if="usuario">
          <a v-on:click="sair()">Sair</a>
        </li>
      </Menu>
    </header>

    <main>
      <div class="container">
        <div class="row">
          <Grid tamanho="3">
            <slot name="side"></slot>
          </Grid>
          <Grid tamanho="9">
            <slot name="principal"></slot>
          </Grid>
        </div>
      </div>
    </main>

    <footer></footer>
  </span>
</template>

<script>
import Menu from "@/components/Estaticos/Menu";
import CompPerfil from "@/components/Dinamicos/CompPerfil";
import Grid from "@/components/Estaticos/Grid";

export default {
  name: "TemplateSite",
  components: {
    Menu,
    CompPerfil,
    Grid
  },
  data() {
    return {
      usuario: false
    };
  },
  created() {
    let sessionUsuario = this.$store.getters.getUsuario;
    if (sessionUsuario) {
      this.usuario = this.$store.getters.getUsuario;
    } else {
      this.$router.push("/Login");
    }
  },
  methods: {
    sair() {
      this.$store.commit("setUsuario", null);
      sessionStorage.clear();
      this.usuario = false;
      this.$router.push("/Login");
    }
  }
};
</script>
