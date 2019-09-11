<template>
  <div class="row">
    <Grid tamanho="12" class="input-field">
      <input type="text" v-model="conteudo.titulo" />
      <textarea
        placeholder="Conteúdo"
        v-if="conteudo.titulo"
        v-model="conteudo.texto"
        class="materialize-textarea"
      ></textarea>
      <input
        type="text"
        v-if="conteudo.titulo && conteudo.texto"
        placeholder="Link"
        v-model="conteudo.link"
      />
      <input
        type="text"
        v-if="conteudo.titulo && conteudo.texto"
        placeholder="Imagem"
        v-model="conteudo.imagem"
      />
      <label for="first_name">O que está acontencendo ?</label>
      <button
        @click="addConteudo"
        v-if="conteudo.titulo && conteudo.texto"
        class="btn green right"
      >Publicar</button>
    </Grid>
  </div>
</template>
<script>
import Grid from "@/components/Estaticos/Grid";
export default {
  name: "Publicar",
  components: { Grid },
  data() {
    return {
      conteudo: { titulo: "", texto: "", link: "", imagem: "" }
    };
  },
  methods: {
    addConteudo() {
      this.$http
        .post(this.$urlAPI + `conteudo/adicionar`, this.conteudo, {
          headers: { authorization: "Bearer " + this.$store.getters.getToken }
        })
        .then(response => {
          if (response.data.status) {
            console.log(response.data.conteudos);
            this.conteudo = { titulo: "", texto: "", link: "", imagem: "" };
            this.$store.commit(
              "setTodosConteudos",
              response.data.conteudos.data
            );
          } else if (response.data.status == false && response.data.validacao) {
            let erros = "";
            for (let erro of Object.values(response.data.erros)) {
              erros += erro + " ";
            }
            alert(erros);
          }
        })
        .catch(err => {
          alert("Erro! Tente novamente mais tarde.");
        });
    }
  }
};
</script>
