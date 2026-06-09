import { applyMiddleware, createStore } from 'redux'
import thunk from 'redux-thunk'
// import testReducer from '../reducer/testReducer'

// const reducer = combineReducers({
//     test: testReducer,
//     // declare other reducers here,
//     // the key name here will be used to select the value from state
// })
import rootReducer from '../reducer/rootReduser'

const store = createStore(rootReducer, applyMiddleware(thunk))
export default store