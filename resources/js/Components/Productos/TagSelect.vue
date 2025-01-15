<script>
import VueMultiselect from "vue-multiselect";
import "vue-multiselect/dist/vue-multiselect.css";

export default {
    components: { VueMultiselect },
    data() {
        return {
            taggingSelected: [],
            taggingOptions: ["Etiqueta1", "Etiqueta2", "Etiqueta3", "Etiqueta4", "Etiqueta5"],
        };
    },
    methods: {
        addTag(newTag) {
            if (!this.taggingOptions.includes(newTag)) {
                this.taggingOptions.push(newTag);
            }
        },
    },
};
</script>

<template>
    <div>
        <VueMultiselect
            v-model="taggingSelected"
            :options="taggingOptions"
            :multiple="true"
            :taggable="true"
            @tag="addTag"
            tag-placeholder="Seleccione valores de atributos"
            placeholder="Seleccione valores de atributos"
            :close-on-select="false"
            :clear-on-select="false"
            :allow-empty="true"
            class="custom-multiselect shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        />
    </div>
</template>

<style>
.custom-multiselect {
    border: 1px solid #6b7280;
    border-radius: 0.375rem;
    padding: 0.5rem;
    font-size: 1rem;
    background-color: #fff;
}

.custom-multiselect .multiselect__tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 20px;
    max-height: 6rem;
    overflow-y: auto;
}

.custom-multiselect .multiselect__tag {
    color: white;
    border-radius: 0.375rem;
    padding: 0.3rem 1rem;
    font-size: 0.875rem;
    flex: 1 1 calc(33% - 0.5rem);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.custom-multiselect .multiselect__tag-icon {
    color: white !important;
    margin-top: 1px;
    cursor: pointer;
    margin-right: -2px;
}

.custom-multiselect .multiselect__placeholder {
    color: #6b7280;
}
</style>
