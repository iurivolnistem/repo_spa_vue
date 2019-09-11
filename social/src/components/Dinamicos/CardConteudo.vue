<template>
  <Grid tamanho="12">
    <div class="card">
      <div class="card-user">
        <figure>
          <img :src="perfil" class="circle" />
        </figure>
        <router-link :to="'/Usuario/'+usuarioId+'/'+$slug(nome, {lower:true})">
          <strong>{{nome}}</strong>
        </router-link>
        <small class="right">{{data}}</small>
      </div>
      <div v-if="img && img != '#'" class="card-image waves-effect waves-light">
        <img :src="img" class="activator" />
      </div>
      <div class="card-content">
        <span class="card-title activator">
          {{titulo}}
          <i class="material-icons right">expand_more</i>
        </span>
        <p v-if="link && link !='#'">
          <a :href="link" target="_blank">Clique para ver a matéria</a>
        </p>
      </div>
      <div class="card-reveal">
        <span class="card-title">
          <i class="material-icons right">close</i>
        </span>
        <p>{{conteudo}}</p>
      </div>
      <div class="card-action">
        <p>
          <a class="actions" @click="curtida(cod)">
            <span>{{totalCurtidas}}</span>
            <i class="material-icons">{{curtiu}}</i>
          </a>
          <a class="actions" @click="abreComentarios()">
            <span>{{listadeConteudo.length}}</span>
            <i class="material-icons">insert_comment</i>
          </a>
        </p>
      </div>
      <div class="card-action" v-if="exibirComentario && listadeConteudo.length > 0">
        <ul class="collection">
          <li class="collection-item avatar" v-for="item in comentarios" :key="item.id">
            <img :src="item.user.imagem" class="circle" />
            <span class="title">
              {{item.user.name}}
              <small>- {{item.data}}</small>
            </span>
            <p>{{item.texto}}</p>
          </li>
        </ul>
      </div>
      <div class="card-action" v-if="exibirComentario">
        <input type="text" v-model="texto_comentario" placeholder="Comentário" />
        <button
          type="button"
          v-if="texto_comentario"
          @click="comentar(cod)"
          class="btn waves-effect waves-light orange"
        >Comentar</button>
      </div>
    </div>
  </Grid>
</template>

<script>
import Grid from "@/components/Estaticos/Grid";
export default {
  name: "CardConteudo",
  components: { Grid },
  props: [
    "cod",
    "nome",
    "perfil",
    "data",
    "img",
    "link",
    "titulo",
    "conteudo",
    "totalcurtidas",
    "conteudocurtido",
    "comentarios",
    "usuarioId"
  ],
  data() {
    return {
      curtiu: this.conteudocurtido ? "favorite" : "favorite_border",
      totalCurtidas: this.totalcurtidas,
      exibirComentario: false,
      texto_comentario: "",
      listadeConteudo: this.comentarios || []
    };
  },
  methods: {
    curtida(cod) {
      this.$http
        .put(
          this.$urlAPI + `conteudo/curtida/${cod}`,
          {},
          {
            headers: { authorization: "Bearer " + this.$store.getters.getToken }
          }
        )
        .then(response => {
          if (response.data.status) {
            this.totalCurtidas = response.data.curtidas;
            this.$store.commit(
              "setTodosConteudos",
              response.data.lista.conteudos.data
            );
            if (this.curtiu == "favorite_border") {
              this.curtiu = "favorite";
            } else {
              this.curtiu = "favorite_border";
            }
          } else {
            alert(response.data.erro);
          }
        })
        .catch(e => {});
    },
    abreComentarios() {
      this.exibirComentario = !this.exibirComentario;
    },
    comentar(cod) {
      this.$http
        .put(
          this.$urlAPI + `conteudo/comentar/${cod}`,
          { texto: this.texto_comentario },
          {
            headers: { authorization: "Bearer " + this.$store.getters.getToken }
          }
        )
        .then(response => {
          if (response.data.status) {
            this.$store.commit(
              "setTodosConteudos",
              response.data.lista.conteudos.data
            );
            this.texto_comentario = "";
          } else {
            alert(response.data.erro);
          }
        })
        .catch(e => {});
    }
  }
};
</script>

<style scoped>
.card .card-user {
  display: flex;
  align-items: center;
  padding: 10px;
  border-bottom: 3px solid #f4f4f4;
}
.card .card-user figure {
  width: 50px;
  height: 50px;
  margin: 0 10px 0 0;
}
.card .card-user figure img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.card .card-user strong {
  margin-right: 10px;
}
.card .card-user small {
  margin-top: 3px;
}
.card .card-action .actions {
  position: relative;
  display: inline-block;
  width: 30px;
  height: 50px;
  margin-right: 15px !important;
  text-align: center;
  cursor: pointer;
}
.card .card-action .actions span {
  position: absolute;
  font-size: 11px;
  width: 100%;
  left: 0;
  top: 50%;
}
.card .card-action .actions i {
  vertical-align: middle;
}
</style>
