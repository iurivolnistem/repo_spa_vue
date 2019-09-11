<template>
  <TemplateSite>
    <div slot="side">
      <CompPerfil :nome="donoPagina.name" :imagem="donoPagina.imagem" :pagUsuario="this.stringUrl">
        <div slot="listaSeguidores">
          <h3>Quem este usuário segue</h3>
          <ul>
            <li v-for="item in amigos" :key="item.id">
              <a>{{item.name}}</a>
            </li>
            <li v-if="amigos.length == 0">
              <a>Esse usuário não segue ninguém.</a>
            </li>
          </ul>
        </div>
      </CompPerfil>
      <button
        v-if="exibirBtnSeguir"
        @click="amigo(donoPagina.id)"
        class="btn btn-info"
      >{{textoBotao}}</button>
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
      <p class="no-content" v-if="semConteudo">Este usuário ainda não postou nenhum conteúdo!</p>
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
  name: "Usuario",
  components: { TemplateSite, CardConteudo, CompPerfil, Grid, Publicar },
  data() {
    return {
      usuario: false,
      urlProximaPagina: null,
      pararScroll: false,
      semConteudo: false,
      donoPagina: { imagem: "", name: "" },
      exibirBtnSeguir: false,
      stringUrl: {
        function() {
          return String(
            "/Usuario/" +
              donoPagina.id +
              "/" +
              $slug(item.user.name, { lower: true })
          );
        }
      },
      amigos: [],
      amigosLogado: [],
      textoBotao: "Seguir"
    };
  },
  created() {
    let sessionUsuario = this.$store.getters.getUsuario;
    if (sessionUsuario) {
      this.usuario = this.$store.getters.getUsuario;
      this.$http
        .get(this.$urlAPI + `conteudo/pagina/lista/` + this.$route.params.id, {
          headers: { authorization: "Bearer " + this.$store.getters.getToken }
        })
        .then(response => {
          if (response.data.status) {
            this.$store.commit(
              "setTodosConteudos",
              response.data.conteudos.data
            );
            this.urlProximaPagina = response.data.conteudos.next_page_url;
            this.pararScroll = false;
            this.donoPagina = response.data.dono;
            if (this.donoPagina.id != this.usuario.id) {
              this.exibirBtnSeguir = true;
            }

            this.$http
              .get(
                this.$urlAPI +
                  `usuario/listAmigosUsuario/` +
                  this.donoPagina.id,
                {
                  headers: {
                    authorization: "Bearer " + this.$store.getters.getToken
                  }
                }
              )
              .then(response => {
                if (response.data.status) {
                  this.amigos = response.data.amigos;
                  this.amigosLogado = response.data.amigosLogado;
                  this.isAmigo();
                } else {
                  alert(response.data.erro);
                }
              })
              .catch(e => {});
          } else {
            this.$router.push("/Home");
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
          if (response.data.status) {
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
    },
    isAmigo() {
      for (let amigo of this.amigosLogado) {
        if (amigo.id == this.donoPagina.id) {
          console.log("Deixar de seguir");
        }
      }
      console.log("Seguir");
    },
    amigo(id) {
      this.$http
        .post(
          this.$urlAPI + `usuario/amigo`,
          { id: id },
          {
            headers: { authorization: "Bearer " + this.$store.getters.getToken }
          }
        )
        .then(response => {
          if (response.data.status) {
            this.amigosLogado = response.data.amigos;
            this.isAmigo();
          } else {
            alert(response.data.erro);
          }
        })
        .catch(e => {});
    }
  }
};
</script>
