import Vue from 'vue'
import Router from 'vue-router'
import Home from '@/pages/Home'
import Login from '@/pages/Login'
import Cadastro from '@/pages/Cadastro'
import Perfil from '@/pages/Perfil'
import Usuario from '@/pages/Usuario'

Vue.use(Router)

export default new Router({
  mode: 'history',
  routes: [{
      path: '/',
    },
    {
      path: '/Home',
      name: 'Home',
      component: Home
    },
    {
      path: '/Usuario/:id/:nome?',
      name: 'Usuario',
      component: Usuario
    },
    {
      path: '/Login',
      name: 'Login',
      component: Login
    },
    {
      path: '/Cadastro',
      name: 'Cadastro',
      component: Cadastro
    },
    {
      path: '/Perfil',
      name: 'Perfil',
      component: Perfil
    }
  ]
})
