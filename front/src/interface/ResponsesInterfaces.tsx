export type StatusType = "error" | "success";

export interface LoginResponseInterface {
  status: StatusType;
  message?: string;
  token?: string;
  username?: string;
}
