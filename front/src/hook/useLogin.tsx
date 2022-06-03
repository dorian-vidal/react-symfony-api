import { LoginResponseInterface } from "../Interface/ResponsesInterfaces";
import axios from "axios";

export default function useLogin() {
  return (
    username: string,
    password: string
  ): Promise<LoginResponseInterface> => {
    return axios
      .get("http://localhost:8245/login.php", {
        withCredentials: true,
        auth: {
          username: username,
          password: password,
        },
      })
      .then((res) => res.data);
  };
}
