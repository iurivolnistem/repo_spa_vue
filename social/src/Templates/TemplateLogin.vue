<template>
  <span>
    <header>
      <Menu url="http://instagram.com.br">
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
        <slot></slot>
      </div>
    </main>
  </span>
</template>

<script>
import Menu from "@/components/Estaticos/Menu";
import Grid from "@/components/Estaticos/Grid";

export default {
  name: "TemplateLogin",
  components: { Menu, Grid },
  data() {
    return {
      usuario: false
    };
  },
  created() {
    let sessionUsuario = this.$store.getters.getUsuario;
    if (sessionUsuario) {
      this.usuario = this.$store.getters.getUsuario;
      this.$router.push("/Home");
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
<style scoped>
main {
  display: flex;
  justify-content: center;
  align-items: center;
  height: calc(100vh - 65px);
}
</style>
