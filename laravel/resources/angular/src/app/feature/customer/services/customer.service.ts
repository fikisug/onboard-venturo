import { Injectable } from '@angular/core';
import { LandaService } from 'src/app/core/services/landa.service';

@Injectable({
  providedIn: 'root'
})
export class CustomerService {

  constructor(private landaService: LandaService) { }


  // getRoles(arrParameter = {}) {
  //   return this.landaService.DataGet('/v1/roles', arrParameter);
  // }

  getUsers(arrParameter = {}) {
    return this.landaService.DataGet('/v1/customers', arrParameter);
  }

  getUserById(userId) {
    return this.landaService.DataGet('/v1/customers/' + userId);
  }

  createUser(payload) {
    return this.landaService.DataPost('/v1/customers', payload);
  }

  updateUser(payload) {
    return this.landaService.DataPut('/v1/customers', payload);
  }

  deleteUser(userId) {
    return this.landaService.DataDelete('/v1/customers/' + userId);
  }
}
