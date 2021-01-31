
async function get(url){
    if(type == 'json'){
        return await fetch(url).then(response => response.json());
    }else{
        return await fetch(url).then(response => response.text());
    }
}

async function post(url, data, type='text'){

    let form = new FormData();
    for(let key in data) {
        form.append(key, data[key]);
    }

    let option = {method:'POST', body:form};
    if(type == 'json'){
        return await fetch(url, option).then(response => response.json());
    }else{
        return await fetch(url, option).then(response => response.text());
    }

}


class Form {

    constructor(id, url=''){

        this.option = {
            'header':new Headers(),
            'method':'POST',
            body: new FormData(document.getElementById(id))
        };
        this.url = url;
        this.type = 'text';
    }

    text(value='text'){
        this.type = value;
        return this;
    }

    append(key, value){
        this.option.body.append(key, value);
        return this;
    }

    header(value = null){
        if(value) this.option.header = new Headers(value);
        return this;
    }

    method(value = 'POST'){
        if(value) this.option.method = value;
        return this;
    }

    data(value = null){
        if(value){
            for(let key in value) {
                this.append(key, value[key]);
            }
        }
        return this;
    }

    async send(method, data){
        this.data(data);
        this.method(method);
        if(this.type == 'json'){
            return await fetch(this.url, this.option).then(response => response.json());
        }else{
            return await fetch(this.url, this.option).then(response => response.text());
        }
    }

    post(data = null){
        return this.send('POST', data);
    }

}
