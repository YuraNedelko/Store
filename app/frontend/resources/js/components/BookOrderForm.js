import React, {Component, Fragment} from 'react';
import {connect} from "react-redux";
import {
    resetViewForm,
    sendingViewFormRequest,
    viewFormErrors,
    viewFormFailed,
    viewFormSuccess
} from "../actions/BookActions";



class BookOrderForm extends Component {
    constructor(props) {
        super(props);
        this.submit = this.handleSubmit.bind(this);
    }

    componentDidMount() {
        this.props.dispatch(resetViewForm());
    }

    handleSubmit(e) {
        e.preventDefault();
        this.props.dispatch(sendingViewFormRequest());
        const data = new FormData(e.target);
        this.sendRequest(data);
    }

    sendRequest(data) {
        fetch(`${address}/home/order/${this.props.book.bookInfo.id}`, {
            method: 'POST',
            headers: {
                'X-REQUESTED-WITH': 'XMLHttpRequest'
            },
            body: data,
        })
            .then(response => response.json())
            .then(
                response => {
                    if (response.errors) {
                        this.props.dispatch(viewFormErrors(response.errors));
                    } else if (response.success) {
                        this.props.dispatch(viewFormSuccess());
                    }
                }
            )
            .catch(() => this.props.dispatch(viewFormFailed()));
    }

    render() {
        let requestState = null;
        let content = null;

        if (this.props.sendingFormRequest) {
            requestState = (
                <div className="alert alert-primary" role="alert">
                    Sending request ...
                </div>
            );
        }  else if (this.props.formRequestFailed) {
            requestState = (
                <div className="alert alert-danger" role="alert">
                    Error occurred while sending request, try again later!
                </div>
            );
        } else {
            content = (
                <form className='book-view-container' onSubmit={this.submit}>
                    <label>Name:</label>
                    <input type="text" name="name"/>
                    <div className="validation-error">
                        {this.props.errors && this.props.errors.name ? this.props.errors.name : ''}
                    </div>

                    <label>Surname:</label>
                    <input name="surname"/>
                    <div className="validation-error">
                        {this.props.errors && this.props.errors.surname ? this.props.errors.surname : ''}
                    </div>

                    <label>Book's amount:</label>
                    <input type="number" step="1" name="amount"/>
                    <div className="validation-error">
                        {this.props.errors && this.props.errors.amount ? this.props.errors.amount : ''}
                    </div>

                    <input type='submit' className={'submit-button'} value=' Send order' />
                </form>
            );
        }

        return (
            <Fragment>
                {requestState}
                {content}
            </Fragment>
        );
    }

}

const mapStateToProps = (state) => {
    return {
        book: state.viewBook,
        formRequestErrors: state.viewFormErrors,
        sendingFormRequest: state.sendingViewFormRequest,
        formRequestFailed: state.viewFormFailed,
        formRequestSuccess: state.viewFormSuccess,
        errors: state.viewFormErrors
    };
};

export default connect(mapStateToProps)(BookOrderForm);