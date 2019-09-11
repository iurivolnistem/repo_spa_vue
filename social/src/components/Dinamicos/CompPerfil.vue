<template>
  <div class="perfil">
    <img :src="imagem" :alt="nome" class="responsive-img" />
    <div class="user-infos">
      <div class="user-name">
        <router-link :to="pagUsuario">{{nome}}</router-link>
      </div>
    </div>
    <div class="user-friends">
      <slot name="listaSeguidores"></slot>
    </div>
  </div>
</template>
<script>
import Grid from "@/components/Estaticos/Grid";

export default {
  name: "CompPerfil",
  components: {
    Grid
  },
  props: ["nome", "imagem", "pagUsuario"]
};
</script>
<style scoped>
.perfil {
  padding: 0 0 10px 0;
  background: #fff;
  margin: 1em 0;
  box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14),
    0 3px 1px -2px rgba(0, 0, 0, 0.12), 0 1px 5px 0 rgba(0, 0, 0, 0.2);
}

.perfil img {
  width: 100%;
  object-fit: cover;
  max-height: 200px;
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

.perfil .user-friends {
  margin-top: 30px;
}

.perfil .user-friends h3 {
  font-size: 16px;
  text-align: center;
  text-transform: uppercase;
  font-weight: 700;
  border-top: 1px solid #f0f4f7;
  border-bottom: 1px solid #f0f4f7;
  padding: 16px;
  background: #43a06f;
  color: #fff;
  margin: 0;
}

.perfil .user-friends ul {
  padding-left: 0;
  margin-bottom: 0;
  list-style: none;
  margin: 0;
}

.perfil .user-friends ul li {
  border-bottom: 1px solid #f0f4f7;
}

.perfil .user-friends ul li:last-child {
  border: none;
}

.perfil .user-friends ul > li {
  display: block;
  position: relative;
}

.perfil .user-friends ul > li > a {
  position: relative;
  display: block;
  padding: 10px 15px;
}

.perfil .user-friends ul li a {
  color: #43a047;
  font-size: 14px;
  font-weight: 400;
}
</style>
