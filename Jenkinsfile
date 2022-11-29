pipeline {
    agent any

    stages {
        stage('Build') {
            steps {
			   sh "ssh -o StrictHostKeyChecking=no -i /home/dogovor24_rsa root@10.133.63.142 'cd /var/www/document-service && git pull && bash deploy.sh'"
            }
        }
    }
}
