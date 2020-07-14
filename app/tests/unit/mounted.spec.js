import { expect, assert } from 'chai';

import { shallowMount, createLocalVue } from '@vue/test-utils';

import './localStorage';
import Vuex from '../../src/store/index';
import VueRouter from 'vue-router';

import Signup from '../../src/views/identification/Signup';
import App from '../../src/App';
import Home from '../../src/views/Home';

import FormFields from '../../src/plugins/formField';

describe('App', () => {
    it('has data', () => {
        expect(App.data).to.be.a('function');
    });
});

const localVue = createLocalVue();
localVue.use(VueRouter);
localVue.use(FormFields);
const router = new VueRouter();

describe('Signup - Mounted App', () => {
    const wrapper = shallowMount(Signup, {
        localVue,
        router,
    });

    it('is a Vue Instance', () => {
        expect(wrapper.isVueInstance()).to.equal(true);
        assert.equal(wrapper.isVueInstance(), true);
    });

    it('is in Login Page', () => {
        expect(Signup.name).to.equal('Signup');
        expect(Signup.data()).to.include.all.keys('form');
    });
});
