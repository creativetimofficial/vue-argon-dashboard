import axios from "axios";
//sconst getters = {};
const instance = axios.create({
  headers: {
    "Content-Type": "data-from"
  },
  baseURL: process.env.VUE_APP_API_BASE_URL
 
});

export default instance;
