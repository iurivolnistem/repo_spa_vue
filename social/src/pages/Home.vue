<template>
  <TemplateSite>
    <div slot="side">
      <CompPerfil
        :nome="usuario.name"
        :imagem="usuario.imagem"
        :pagUsuario="'/Usuario/'+usuario.id+'/'+$slug(usuario.name, {lower: true})"
      >
        <div slot="listaSeguidores">
          <h3>Quem você segue</h3>
          <ul>
            <li v-for="item in amigos" :key="item.id">
              <a>{{item.name}}</a>
            </li>
            <li v-if="amigos.length == 0">
              <a>Você não está seguindo ninguém.</a>
            </li>
          </ul>
        </div>
      </CompPerfil>
    </div>
    <div slot="principal" v-scroll="handleScroll">
      <Publicar />
      <div class="row">
        <CardConteudo
          :totalcurtidas="item.total_curtidas"
          :comentarios="item.comentarios"
          :conteudocurtido="item.conteudo_curtido"
          :cod="item.id"
          :usuarioId="item.user.id"
          :perfil="item.user.imagem"
          :nome="item.user.name"
          :data="item.data"
          :img="item.imagem"
          :link="item.link"
          :titulo="item.titulo"
          :conteudo="item.texto"
          v-for="item in listadeConteudos"
          :key="item.id"
        ></CardConteudo>
      </div>
    </div>
  </TemplateSite>
</template>

<script>
import TemplateSite from "@/Templates/TemplateSite";
import CardConteudo from "@/components/Dinamicos/CardConteudo";
import CompPerfil from "@/components/Dinamicos/CompPerfil";
import Grid from "@/components/Estaticos/Grid";
import Publicar from "@/components/Estaticos/Publicar";

export default {
  name: "Home",
  components: { TemplateSite, CardConteudo, CompPerfil, Grid, Publicar },
  data() {
    return {
      usuario: false,
      urlProximaPagina: null,
      pararScroll: false,
      stringUrl: {
        function() {
          return String("/Usuario/");
        }
      },
      amigos: []
    };
  },
  created() {
    let sessionUsuario = this.$store.getters.getUsuario;
    if (sessionUsuario) {
      this.usuario = this.$store.getters.getUsuario;
      this.$http
        .get(this.$urlAPI + `conteudo/lista`, {
          headers: { authorization: "Bearer " + this.$store.getters.getToken }
        })
        .then(response => {
          if (response.data.status) {
            this.$store.commit(
              "setTodosConteudos",
              response.data.conteudos.data
            );
            this.urlProximaPagina = response.data.conteudos.next_page_url;

            this.$http
              .get(this.$urlAPI + `usuario/listamigos`, {
                headers: {
                  authorization: "Bearer " + this.$store.getters.getToken
                }
              })
              .then(response => {
                if (response.data.status) {
                  console.log(response);
                  this.amigos = response.data.amigos;
                } else {
                  alert(response.data.erro);
                }
              })
              .catch(e => {});

            this.pararScroll = false;
          }
        })
        .catch(e => {
          console.log(e);
        });
    }
  },
  computed: {
    listadeConteudos() {
      return this.$store.getters.getTodosConteudos;
    }
  },
  methods: {
    carregarConteudo() {
      if (!this.urlProximaPagina) {
        return;
      }
      this.$http
        .get(this.urlProximaPagina, {
          headers: { authorization: "Bearer " + this.$store.getters.getToken }
        })
        .then(response => {
          if (response.data.status && this.$route.name == "Home") {
            this.$store.commit(
              "setCarregarMaisConteudos",
              response.data.conteudos.data
            );
            this.urlProximaPagina = response.data.conteudos.next_page_url;
            this.pararScroll = false;
          }
        })
        .catch(e => {
          console.log(e);
        });
    },
    handleScroll() {
      if (this.pararScroll) {
        return;
      }
      if (window.scrollY >= document.body.clientHeight - 950) {
        this.pararScroll = true;
        this.carregarConteudo();
      }
    }
  }
};
</script>
