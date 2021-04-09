<template>
  <v-card>
    <v-card-text>
      <v-row>
        <v-col cols="5">
          <v-text-field id="cod" label="CPF ou CNPJ"></v-text-field>
        </v-col>

        <v-col cols="5">
          <v-text-field id="senha" type="password" label="Senha"></v-text-field>
        </v-col>

        <v-col cols="2">
          <v-btn depressed id="consulta" dark color="success" @click="consulta">
            consultar
          </v-btn>
        </v-col>
      </v-row>
    </v-card-text>
  </v-card>
</template>

<script>
// @ is an alias to /src

export default {
  name: "CardAutenticao",
  methods: {
    consulta: function () {
      let cod = document.getElementById("cod");
      let senha = document.getElementById("senha");

      let dados =
        cod.length === 11
          ? { cpf: cod, senha: senha }
          : { cnpj: cod, senha: senha };

      this.axios
        .post("https://8000-crimson-iguana-vq8b8m6l.ws-us03.gitpod.io", dados)
        .then(function (response) {
          console.log(response);
        })
        .catch(function (error) {
          console.log(error);
        });
    },
  },
};
</script>
