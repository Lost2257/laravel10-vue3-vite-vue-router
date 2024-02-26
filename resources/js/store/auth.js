// store/auth.js
import axios from 'axios';

export default {
    namespaced: true,
    state: {
        loggedIn: false,
        name: '',
        ipList: [],
        autoLoginDone: false,
        roles: '',
      },
      mutations: {
        setLoggedIn(state, { loggedIn, name, roles }) {
          console.log('Setting loggedIn to:', loggedIn);
          state.loggedIn = loggedIn;
          state.name = name;
          state.roles = roles;
        },
        updateIpList(state, ipList) {
          console.log('Updating IP list:', ipList);
          state.ipList = ipList;
        },
        setAutoLoginDone(state, value) {
          state.autoLoginDone = value;
        },
        setUserRoles(state, roles) {
          state.roles = roles
        },
      },
    actions: {
        async autoLogin({ commit, state }) {
          if (!state.autoLoginDone) {
            try {
                const loginResponse = await axios.post('/auto-login');
                console.log('Auto-login successful:', loginResponse.data);

              commit('setLoggedIn', {
                loggedIn: true,
                name: loginResponse.data.name,
                roles: loginResponse.data.role,
              });

              commit('setUserRoles', loginResponse.data.roles);
              commit('setAutoLoginDone', true);
            } catch (error) {
              console.error('Error during auto-login:', error);
            }
          }
        },
      },

}
