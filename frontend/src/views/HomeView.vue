<template>
    <div class="form_wrapper container mt-5">
        <div class="row d-flex justify-content-center">
            <div class="col-md-6">
                <div class="card px-5 py-5">
                    <form @submit.prevent="addLead" onsubmit="return false"
                        class="form row g-3 justify-content-center align-items-center">
                        <div class="col-md-12">
                            <input v-model="name" class="form-control" id="name" type="text" name="name" required
                                placeholder="Имя">
                        </div>
                        <div class="col-md-12">
                            <input v-model="email" class="form-control" id="email" type="email" name="email" required
                                placeholder="Электронная почта">
                        </div>
                        <div class="col-md-12">
                            <input v-model="tel" class="form-control" id="tel" type="tel" name="tel" required
                                placeholder="Номер телефона">
                        </div>
                        <div class="col-md-12">
                            <input v-model="price" class="form-control" id="price" type="number" step="0.01" name="price"
                                required placeholder="Цена">
                        </div>

                        <div class="col-6">
                            <button class="btn btn-success">Отправить данные</button>
                        </div>
                        <div class="col-6">
                            <AuthBtn />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { AuthBtn } from "@/components";
import { ref, computed, watch } from 'vue';
import api from "@/api"

const props = defineProps({
    name: {
        type: String,
    },
    email: {
        type: String,
    },
    tel: {
        type: String,
    },
    price: {
        type: Number
    }
});

const name = ref(props.name);
const email = ref(props.email);
const tel = ref(props.tel);
const price = ref(props.price);

const addLead = async (event) => {
    const result = await api.amoCrm.leadAdd(
        {
            name: name.value,
            email: email.value,
            tel: tel.value,
            price: price.value
        },
        {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        }
    );


    if (result.data.status == 'error') {
        alert(result.data.message);
    } else if (result.data.status == "success") {
        alert("Данные успешно добавлены!");
    } else {
        alert("Ошибка на стороне сервера, обратитесь к администратору");
    }
    console.log(result);
}
</script>