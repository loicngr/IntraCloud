import { expect } from 'chai';
import { login } from './setup';

import './fetch';

describe('User Auth', () => {
    it('User is connected ?', async () => {
        const userAuth = await login();
        expect(userAuth).to.be.an('object');
    });
});
